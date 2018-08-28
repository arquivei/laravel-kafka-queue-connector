<?php

namespace SqsQueueConnector\Tests\Queue\Jobs;

use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use KafkaQueueConnector\Queue\Jobs\KafkaJob;

class KafkaJobTest extends TestCase
{
    public function testInstanceOf()
    {
        $kafkaJob = $this->createMock(KafkaJob::class);
        $this->assertInstanceOf(KafkaJob::class, $kafkaJob);
    }

    public function testMethods()
    {
        $kafkaJob = $this->createMock(KafkaJob::class);
        $this->assertTrue(method_exists($kafkaJob, 'fire'));
        $this->assertTrue(method_exists($kafkaJob, 'delete'));
        $this->assertTrue(method_exists($kafkaJob, 'release'));
        $this->assertTrue(method_exists($kafkaJob, 'attempts'));
        $this->assertTrue(method_exists($kafkaJob, 'getJobId'));
        $this->assertTrue(method_exists($kafkaJob, 'setJobId'));
        $this->assertTrue(method_exists($kafkaJob, 'getRawBody'));
        $this->assertTrue(method_exists($kafkaJob, 'unserialize'));
        $this->assertTrue(method_exists($kafkaJob, 'getValueValidation'));
    }
}