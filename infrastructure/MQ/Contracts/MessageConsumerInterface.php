<?php

declare(strict_types=1);

namespace Infrastructure\MQ\Contracts;

use Closure;

/**
 * Contract for message consumers across different broker drivers (RabbitMQ, Kafka, Pub/Sub, etc.).
 */
interface MessageConsumerInterface
{
    /**
     * Start consuming messages from a specific queue or topic.
     *
     * @param  string  $queueOrTopic  The queue or topic to consume from
     * @param  Closure  $handler  The callback handler receives ($payload, $rawMessage)
     * @param  int  $prefetchCount  Number of unacknowledged messages to fetch at a time
     */
    public function consume(string $queueOrTopic, Closure $handler, int $prefetchCount = 1): void;
}
