<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

use Infrastructure\MQ\Contracts\MessagePublisherInterface;
use Infrastructure\MQ\Exceptions\BrokerConnectionException;
use Infrastructure\MQ\Exceptions\MessagePublishFailedException;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * RabbitMQ implementation of MessagePublisherInterface.
 */
class Publisher implements MessagePublisherInterface
{
    public function __construct(
        private RabbitMQConnection $connection,
        private ?RabbitMQTopology $topology = null,
    ) {
        $this->topology = $topology ?? new RabbitMQTopology($this->connection);
    }

    /**
     * Publish a persistent JSON message to an exchange with optional TTL.
     *
     * @throws BrokerConnectionException
     * @throws MessagePublishFailedException
     */
    public function publish(string $topicOrExchange, string $routingKey, array $payload, ?int $ttl = null): void
    {
        try {
            if ($topicOrExchange === '' && $routingKey !== '') {
                $this->topology->declareQueue($routingKey);
            }

            $channel = $this->connection->getChannel();

            $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

            $properties = [
                'content_type'  => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'timestamp'     => time(),
            ];

            if ($ttl !== null && $ttl > 0) {
                $properties['expiration'] = (string) $ttl;
            }

            $message = new AMQPMessage($body, $properties);

            $channel->basic_publish(
                msg: $message,
                exchange: $topicOrExchange,
                routing_key: $routingKey
            );
        } catch (BrokerConnectionException $e) {
            throw $e;
        } catch (Throwable $th) {
            throw MessagePublishFailedException::forPayload(
                exchange: $topicOrExchange,
                routingKey: $routingKey,
                payload: $payload,
                previous: $th
            );
        }
    }
}
