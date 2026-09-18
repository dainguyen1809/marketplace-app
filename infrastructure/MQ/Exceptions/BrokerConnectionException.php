<?php

declare(strict_types=1);

namespace Infrastructure\MQ\Exceptions;

use Infrastructure\Exceptions\ConnectionException;
use Throwable;

class BrokerConnectionException extends ConnectionException
{
    public static function failedToConnect(string $host, int $port, ?Throwable $previous = null): self
    {
        return new self(
            service: 'rabbitmq',
            host: $host,
            port: $port,
            message: "Failed to connect to message broker {$host}:{$port}",
            previous: $previous
        );
    }

    public static function failedToClose(string $host, int $port, ?Throwable $previous = null): self
    {
        return new self(
            service: 'rabbitmq',
            host: $host,
            port: $port,
            message: "Failed to cleanly close connection to message broker {$host}:{$port}",
            previous: $previous
        );
    }
}
