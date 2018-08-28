<?php

namespace KafkaQueueConnector\Queue\Jobs;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Jobs\JobName;
use Illuminate\Container\Container;
use KafkaQueueConnector\Queue\KafkaQueue;
use Illuminate\Database\DetectsDeadlocks;
use Illuminate\Contracts\Queue\Job as JobContract;

/**
 * Class KafkaJob
 * @package KafkaQueueConnector\Queue\Jobs
 */
class KafkaJob extends Job implements JobContract
{
    use DetectsDeadlocks;

    /**
     * @var array
     */
    private $consumers;

    /**
     * @var \RdKafka\Message
     */
    private $message;

    /**
     * @var KafkaQueue
     */
    private $connection;

    /**
     * KafkaJob constructor.
     * @param Container $container
     * @param KafkaQueue $connection
     * @param \RdKafka\Message $message
     * @param $connectionName
     * @param $queue
     * @param array $consumers
     */
    public function __construct(
        Container $container,
        KafkaQueue $connection,
        \RdKafka\Message $message,
        $connectionName,
        $queue,
        array $consumers
    ) {
        $this->queue = $queue;
        $this->message = $message;
        $this->consumers = $consumers;
        $this->container = $container;
        $this->connection = $connection;
        $this->connectionName = $connectionName;
    }

    /**
     * Fire the job.
     *
     * @throws Exception
     */
    public function fire()
    {
        try {
            $payload = $this->payload();
            list($class, $method) = JobName::parse($payload['job']);

            with($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
        } catch (Exception $exception) {
            if (
                $this->causedByDeadlock($exception) ||
                Str::contains($exception->getMessage(), ['detected deadlock'])
            ) {
                sleep(2);
                $this->fire();

                return;
            }

            throw $exception;
        }
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int)($this->payload()['attempts']) + 1;
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        $job = $this->message->payload;

        $classConsumer = null;
        foreach ($this->consumers as $consumer) {
            $validation = $this->getValueValidation($consumer);
            if ($validation === $consumer['validation']['value']) {
                $classConsumer = $consumer['job'];
                break;
            }
        }
        if (!is_null($classConsumer)) {
            $job = json_encode([
                'displayName' => $classConsumer,
                'job' => 'Illuminate\Queue\CallQueuedHandler@call',
                'maxTries' => null,
                'timeout' => null,
                'timeoutAt' => null,
                'data' => [
                    'commandName' => $classConsumer,
                    'command' => serialize(new $classConsumer(json_decode($job))),
                ],
            ]);
        }

        return $job;
    }

    /**
     * @param array $consumer
     * @return mixed
     */
    private function getValueValidation(array $consumer)
    {
        $validation = json_decode($this->message->payload);
        foreach ($consumer['validation']['key'] as $key) {
            $validation = $validation->$key ?? $validation;
        }
        return $validation;
    }

    /**
     * Delete the job from the queue.
     */
    public function delete()
    {
        parent::delete();
        $this->connection->getConsumer()->commitAsync($this->message);
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     *
     * @throws Exception
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $this->delete();

        $body = $this->payload();

        /*
         * Some jobs don't have the command set, so fall back to just sending it the job name string
         */
        if (isset($body['data']['command']) === true) {
            $job = $this->unserialize($body);
        } else {
            $job = $this->getName();
        }

        $data = $body['data'];

        if ($delay > 0) {
            $this->connection->later($delay, $job, $data, $this->getQueue());
        } else {
            $this->connection->push($job, $data, $this->getQueue());
        }
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->message->key;
    }

    /**
     * Sets the job identifier.
     *
     * @param string $id
     */
    public function setJobId($id)
    {
        $this->connection->setCorrelationId($id);
    }

    /**
     * Unserialize job.
     *
     * @param array $body
     *
     * @throws Exception
     *
     * @return mixed
     */
    private function unserialize(array $body)
    {
        try {
            return unserialize($body['data']['command']);
        } catch (Exception $exception) {
            if (
                $this->causedByDeadlock($exception) ||
                Str::contains($exception->getMessage(), ['detected deadlock'])
            ) {
                sleep(2);

                return $this->unserialize($body);
            }

            throw $exception;
        }
    }
}