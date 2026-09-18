<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

use Closure;
use Illuminate\Support\Facades\Log;
use Infrastructure\MQ\Contracts\MessageConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * RabbitMQ implementation of MessageConsumerInterface.
 */
class Consumer implements MessageConsumerInterface
{
    public function __construct(
        private RabbitMQConnection $connection,
        private RabbitMQTopology $topology
    ) {}

    /**
     * Start consuming messages from a queue with manual ACK/NACK and DLQ forwarding.
     */
    public function consume(string $queueOrTopic, Closure $handler, int $prefetchCount = 1): void
    {
        $channel = $this->connection->getChannel();

        // Ensure queue and DLX are declared
        $this->topology->declareQueue($queueOrTopic);

        $channel->basic_qos(
            prefetch_size: 0,
            prefetch_count: $prefetchCount,
            a_global: false
        );

        $callback = function (AMQPMessage $message) use ($handler, $queueOrTopic): void {
            try {
                $payload = json_decode($message->getBody(), true, JSON_THROW_ON_ERROR);

                $handler($payload, $message);

                // Success: manual ACK to remove message from queue
                $message->ack();
            } catch (Throwable $th) {
                // Log failure with error context
                Log::channel('rabbitmq')->error("Failed to process message from queue [{$queueOrTopic}]: {$th->getMessage()}", [
                    'queue'     => $queueOrTopic,
                    'message'   => $message->getBody(),
                    'exception' => $th,
                ]);

                // Failed: send NACK with requeue=false to forward to DLQ
                $message->nack(false);
            }
        };

        // Register consumer (no_ack = false for manual ACK)
        $channel->basic_consume(
            queue: $queueOrTopic,
            consumer_tag: '',
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: $callback
        );

        // Worker event loop
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
