<?php

namespace KafkaQueueConnector\Queue\Connectors;

use KafkaQueueConnector\Queue\KafkaQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

/**
 * Class KafkaConnector
 * @package KafkaQueueConnector\Queue\Connectors
 */
class KafkaConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $conf = $this->setConf($config);

        $producer = new \RdKafka\Producer($conf);
        $producer->addBrokers($config['brokers']);

        $consumer = new \RdKafka\KafkaConsumer($conf);

        return new KafkaQueue(
            $producer,
            $consumer,
            $config
        );
    }

    /**
     * @param array $config
     * @return \RdKafka\Conf
     */
    private function setConf(array $config): \RdKafka\Conf
    {
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.offset.reset', 'largest');

        $conf = new \RdKafka\Conf();
        $conf->set('group.id', $config['group.id']);
        $conf->set('metadata.broker.list', $config['brokers']);
        $conf->set('enable.auto.commit', 'false');
        $conf->set('offset.store.method', 'broker');
        $conf->setDefaultTopicConf($topicConf);

        $conf->set('security.protocol', $config['security.protocol']);

        if ($config['security.protocol'] == 'SASL_PLAINTEXT') {
            $conf->set('sasl.mechanisms', $config['sasl']['mechanisms']);
            $conf->set('sasl.username', $config['sasl']['username']);
            $conf->set('sasl.password', $config['sasl']['password']);
        }

        return $conf;
    }
}