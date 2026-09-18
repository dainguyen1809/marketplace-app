<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

use Closure;
use Infrastructure\MQ\Contracts\MessageConsumerInterface;
use Infrastructure\MQ\Contracts\MessagePublisherInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Facade coordinating RabbitMQ Connection, Topology, Publisher, and Consumer.
 */
class RabbitMQClient implements MessageConsumerInterface, MessagePublisherInterface
{
    private RabbitMQConnection $connection;

    private RabbitMQTopology $topology;

    private Publisher $publisher;

    private Consumer $consumer;

    public function __construct(
        ?RabbitMQConfig $config = null,
        ?RabbitMQConnection $connection = null,
        ?RabbitMQTopology $topology = null,
        ?Publisher $publisher = null,
        ?Consumer $consumer = null
    ) {
        $this->connection = $connection ?? new RabbitMQConnection($config);
        $this->topology = $topology ?? new RabbitMQTopology($this->connection);
        $this->publisher = $publisher ?? new Publisher($this->connection);
        $this->consumer = $consumer ?? new Consumer($this->connection, $this->topology);
    }

    /**
     * Publish a message to an exchange with routing key and optional TTL.
     */
    public function publish(string $topicOrExchange, string $routingKey, array $payload, ?int $ttl = null): void
    {
        $this->publisher->publish($topicOrExchange, $routingKey, $payload, $ttl);
    }

    /**
     * Start consuming messages from a queue with manual ACK/NACK and DLQ forwarding.
     */
    public function consume(string $queueOrTopic, Closure $handler, int $prefetchCount = 1): void
    {
        $this->consumer->consume($queueOrTopic, $handler, $prefetchCount);
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
        $this->topology->declareQueue($queue, $exchange, $routingKey, $withDlx);
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
        $this->topology->declareExchange($exchange, $type, $durable, $autoDelete);
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection->getConnection();
    }

    public function getChannel(): AMQPChannel
    {
        return $this->connection->getChannel();
    }

    public function close(): void
    {
        $this->connection->close();
    }

    public function getTopology(): RabbitMQTopology
    {
        return $this->topology;
    }

    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    public function getConsumer(): Consumer
    {
        return $this->consumer;
    }
}
