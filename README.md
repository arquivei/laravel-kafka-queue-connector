# Laravel Kafka Queue Connector
Kafka Queue driver for Laravel

## Install

1. Install [librdkafka c library](https://github.com/edenhill/librdkafka)

    ```bash
    $ cd /tmp
    $ mkdir librdkafka
    $ cd librdkafka
    $ git clone https://github.com/edenhill/librdkafka.git .
    $ ./configure
    $ make
    $ make install
    ```
2. Install the [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) PECL extension

    ```bash
    $ pecl install rdkafka
    ```
3. Add the following to your php.ini file to enable the php-rdkafka extension
    `extension=rdkafka.so`
    
4. Install this package via composer using:

	`composer require arquivei/laravel-kafka-queue-connector`
	
5. Add queue config in section `connections` in your `config/queue.php`

```php
    'kafka' => [
        'driver' => 'kafka',
        'queue' => env('KAFKA_QUEUE', 'default'),
        'brokers' => env('KAFKA_BROKERS', 'localhost'),
        'sleep_on_error' => env('KAFKA_ERROR_SLEEP', 5),
        'security.protocol' => env('SECURITY_PROTOCOL', 'PLAINTEXT'),
        'group.id' => env('GROUP_ID', 'php-kafka'),
        'sasl' => [
            'mechanisms' => env('SASL_MECHANISMS'),
            'username' => env('SASL_USERNAME'),
            'password' => env('SASL_PASSWORD'),
        ],
        'consumers' => [
            [
                'validation' => [
                    'key' => [],
                    'value' => '',
                ],
                'job' => YourConsumerJob::class,
            ],
            [
                'validation' => [
                    'key' => [],
                    'value' => '',
                ],
                'job' => YourConsumerJob::class,
            ],
        ],
    ],
```

#### Important

if you want to consume events with custom structures, add `consumers` with the necessary rules

## Run Tests
`$ vendor/bin/phpunit tests`

## TODO
- Add validation for default consumer
- Add any validation option for one consumer