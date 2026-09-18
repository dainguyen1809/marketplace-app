<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

use PhpAmqpLib\Wire\AMQPTable;

/**
 * Handles RabbitMQ topology configuration including Queues, Exchanges, DLX, and Bindings.
 */
class RabbitMQTopology
{
    private bool $dlxInitialized = false;

    public function __construct(
        private RabbitMQConnection $connection,
        private ?RabbitMQConfig $config = null
    ) {
        $this->config = $config ?? $this->connection->getConfig();
    }

    /**
     * Declare Dead Letter Exchange (DLX) and Dead Letter Queue (DLQ) if not already initialized.
     */
    public function setupDeadLetterExchange(bool $force = false): void
    {
        if ($this->dlxInitialized && ! $force) {
            return;
        }

        $channel = $this->connection->getChannel();

        // 1. Declare dead letter exchange (direct, durable=true)
        $channel->exchange_declare($this->config->dlxExchange, 'direct', false, true, false);

        // 2. Declare dead letter queue (durable=true)
        $channel->queue_declare($this->config->dlxQueue, false, true, false, false);

        // 3. Bind dead letter queue to dead letter exchange
        $channel->queue_bind($this->config->dlxQueue, $this->config->dlxExchange, $this->config->dlxRoutingKey);

        $this->dlxInitialized = true;
    }

    /**
     * Declare a durable queue bound with Dead Letter Exchange (DLX).
     */
    public function declareQueue(
        string $queue,
        ?string $exchange = null,
        ?string $routingKey = null,
        bool $withDlx = true
    ): void {
        $channel = $this->connection->getChannel();

        $arguments = null;
        if ($withDlx) {
            $this->setupDeadLetterExchange();

            $arguments = new AMQPTable([
                'x-dead-letter-exchange'    => $this->config->dlxExchange,
                'x-dead-letter-routing-key' => $this->config->dlxRoutingKey,
            ]);
        }

        // Declare queue (durable=true)
        $channel->queue_declare(
            queue: $queue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false,
            nowait: false,
            arguments: $arguments
        );

        if ($exchange !== null) {
            $channel->queue_bind(
                queue: $queue,
                exchange: $exchange,
                routing_key: $routingKey ?? ''
            );
        }
    }

    /**
     * Declare an exchange.
     */
    public function declareExchange(
        string $exchange,
        string $type = 'direct',
        bool $durable = true,
        bool $autoDelete = false
    ): void {
        $channel = $this->connection->getChannel();
        $channel->exchange_declare($exchange, $type, false, $durable, $autoDelete);
    }
}
