<?php

namespace KafkaQueueConnector;

use Illuminate\Support\ServiceProvider;
use KafkaQueueConnector\Queue\Connectors\KafkaConnector;

/**
 * Class KafkaQueueProvider
 * @package KafkaQueueConnector
 */
class KafkaQueueProvider extends ServiceProvider
{
    /**
     * Register the application's event listeners.
     */
    public function boot()
    {
        $queue = $this->app['queue'];
        $queue->addConnector('kafka', function () {
            return new KafkaConnector;
        });
    }
}