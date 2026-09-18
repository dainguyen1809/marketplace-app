<?php

declare(strict_types=1);

namespace Infrastructure\MQ\Contracts;

use Infrastructure\MQ\Exceptions\BrokerConnectionException;
use Infrastructure\MQ\Exceptions\MessagePublishFailedException;

/**
 * Contract for message publishers across different broker drivers (RabbitMQ, Kafka, Pub/Sub, etc.).
 */
interface MessagePublisherInterface
{
    /**
     * Publish a message to the message broker.
     *
     * @param  string  $topicOrExchange  Destination topic, exchange, or channel
     * @param  string  $routingKey  Routing key or partition key
     * @param  array<string, mixed>  $payload  Message payload to be serialized
     * @param  int|null  $ttl  Optional TTL in milliseconds
     *
     * @throws BrokerConnectionException
     * @throws MessagePublishFailedException
     */
    public function publish(string $topicOrExchange, string $routingKey, array $payload, ?int $ttl = null): void;
}
