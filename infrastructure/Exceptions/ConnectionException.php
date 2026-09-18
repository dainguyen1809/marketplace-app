<?php

declare(strict_types=1);

namespace Infrastructure\Exceptions;

use Throwable;

abstract class ConnectionException extends InfrastructureException
{
    public function __construct(
        string $service,
        string $host,
        int|string $port,
        string $message = '',
        ?Throwable $previous = null,
        array $extraContext = []
    ) {
        $context = array_merge([
            'service' => $service,
            'host'    => $host,
            'port'    => (string) $port,
        ], $extraContext);

        $defaultMessage = $message ?: "Failed to connect to {$service} at {$host}:{$port}";

        parent::__construct(
            message: $defaultMessage,
            code: 500,
            previous: $previous,
            context: $context
        );
    }
}
