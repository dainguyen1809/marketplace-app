<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

/**
 * Immutable configuration value object for RabbitMQ connection and DLX settings.
 */
final readonly class RabbitMQConfig
{
    public function __construct(
        public string $host,
        public int $port,
        public string $user,
        public string $password,
        public string $vhost = '/',
        public float $connectionTimeout = 3.0,
        public float $readWriteTimeout = 130.0,
        public int $heartbeat = 60,
        public string $dlxExchange = 'ecommerce.dlx',
        public string $dlxQueue = 'ecommerce.dead_letter_queue',
        public string $dlxRoutingKey = 'dead_letter',
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            host: (string) config('rabbitmq.host', 'rabbitmq'),
            port: (int) config('rabbitmq.port', 5672),
            user: (string) config('rabbitmq.user', 'ecommerce'),
            password: (string) config('rabbitmq.password', 'secret'),
            vhost: (string) config('rabbitmq.vhost', '/'),
            connectionTimeout: (float) config('rabbitmq.connection_timeout', 3.0),
            readWriteTimeout: (float) config('rabbitmq.read_write_timeout', 130.0),
            heartbeat: (int) config('rabbitmq.heartbeat', 60),
            dlxExchange: (string) config('rabbitmq.dlx_exchange', 'ecommerce.dlx'),
            dlxQueue: (string) config('rabbitmq.dlx_queue', 'ecommerce.dead_letter_queue'),
            dlxRoutingKey: (string) config('rabbitmq.dlx_routing', 'dead_letter'),
        );
    }
}
