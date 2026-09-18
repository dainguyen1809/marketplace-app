<?php

declare(strict_types=1);

namespace Infrastructure\MQ\RabbitMQ;

use Infrastructure\MQ\Exceptions\BrokerConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Throwable;

/**
 * Manages the lifecycle of RabbitMQ AMQPStreamConnection and AMQPChannel instances.
 */
class RabbitMQConnection
{
    private ?AMQPStreamConnection $connection = null;

    private ?AMQPChannel $channel = null;

    private RabbitMQConfig $config;

    public function __construct(?RabbitMQConfig $config = null)
    {
        $this->config = $config ?? RabbitMQConfig::fromConfig();
    }

    /**
     * Gets a valid AMQP connection, creating it if needed.
     *
     * @throws BrokerConnectionException
     */
    public function getConnection(): AMQPStreamConnection
    {
        if ($this->connection === null || ! $this->connection->isConnected()) {
            try {
                $this->connection = new AMQPStreamConnection(
                    host: $this->config->host,
                    port: $this->config->port,
                    user: $this->config->user,
                    password: $this->config->password,
                    vhost: $this->config->vhost,
                    insist: false,
                    login_method: 'AMQPLAIN',
                    login_response: null,
                    locale: 'en_US',
                    connection_timeout: $this->config->connectionTimeout,
                    read_write_timeout: $this->config->readWriteTimeout,
                    context: null,
                    keepalive: false,
                    heartbeat: $this->config->heartbeat
                );
            } catch (Throwable $e) {
                $this->close();
                throw BrokerConnectionException::failedToConnect($this->config->host, $this->config->port, $e);
            }
        }

        return $this->connection;
    }

    /**
     * Gets a valid AMQP channel, creating connection and channel if needed.
     *
     * @throws BrokerConnectionException
     */
    public function getChannel(): AMQPChannel
    {
        try {
            if ($this->channel === null || ! $this->channel->is_open()) {
                $connection = $this->getConnection();
                $this->channel = $connection->channel();
            }

            return $this->channel;
        } catch (BrokerConnectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->close();
            throw BrokerConnectionException::failedToConnect($this->config->host, $this->config->port, $e);
        }
    }

    /**
     * Checks if the connection is currently established.
     */
    public function isConnected(): bool
    {
        return $this->connection !== null && $this->connection->isConnected();
    }

    /**
     * Checks if the channel is currently open.
     */
    public function isChannelOpen(): bool
    {
        return $this->channel !== null && $this->channel->is_open();
    }

    /**
     * Explicitly close channel and connection safely.
     */
    public function close(): void
    {
        try {
            if ($this->channel !== null && $this->channel->is_open()) {
                $this->channel->close();
            }
        } catch (Throwable) {
            // Suppress channel close errors
        } finally {
            $this->channel = null;
        }

        try {
            if ($this->connection !== null && $this->connection->isConnected()) {
                $this->connection->close();
            }
        } catch (Throwable) {
            // Suppress connection close errors
        } finally {
            $this->connection = null;
        }
    }

    public function getConfig(): RabbitMQConfig
    {
        return $this->config;
    }

    public function __destruct()
    {
        $this->close();
    }
}
