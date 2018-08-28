<?php

namespace SqsQueueConnector\Tests\Queue;

use PHPUnit\Framework\TestCase;
use KafkaQueueConnector\Queue\KafkaQueue;

class KafkaQueueTest extends TestCase
{
    public function testInstanceOf()
    {
        $kafkaQueue = $this->createMock(KafkaQueue::class);
        $this->assertInstanceOf(KafkaQueue::class, $kafkaQueue);
    }

    public function testMethods()
    {
        $kafkaQueue = $this->createMock(KafkaQueue::class);
        $this->assertTrue(method_exists($kafkaQueue, 'size'));
        $this->assertTrue(method_exists($kafkaQueue, 'push'));
        $this->assertTrue(method_exists($kafkaQueue, 'pushRaw'));
        $this->assertTrue(method_exists($kafkaQueue, 'later'));
        $this->assertTrue(method_exists($kafkaQueue, 'pop'));
        $this->assertTrue(method_exists($kafkaQueue, 'getQueueName'));
        $this->assertTrue(method_exists($kafkaQueue, 'getTopic'));
        $this->assertTrue(method_exists($kafkaQueue, 'setCorrelationId'));
        $this->assertTrue(method_exists($kafkaQueue, 'createPayloadArray'));
        $this->assertTrue(method_exists($kafkaQueue, 'reportConnectionError'));
        $this->assertTrue(method_exists($kafkaQueue, 'getConsumer'));
    }
}