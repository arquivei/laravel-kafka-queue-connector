<?php

namespace SqsQueueConnector\Tests\Queue\Connectors;

use PHPUnit\Framework\TestCase;
use KafkaQueueConnector\Queue\Connectors\KafkaConnector;

class KafkaConnectorTest extends TestCase
{
    public function testInstanceOf()
    {
        $kafkaConnector = $this->createMock(KafkaConnector::class);
        $this->assertInstanceOf(KafkaConnector::class, $kafkaConnector);
    }

    public function testMethods()
    {
        $kafkaConnector = $this->createMock(KafkaConnector::class);
        $this->assertTrue(method_exists($kafkaConnector, 'connect'));
        $this->assertTrue(method_exists($kafkaConnector, 'setConf'));
    }
}