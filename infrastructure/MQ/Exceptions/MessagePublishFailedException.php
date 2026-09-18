<?php

declare(strict_types=1);

namespace Infrastructure\MQ\Exceptions;

use Infrastructure\Exceptions\InfrastructureException;
use Throwable;

class MessagePublishFailedException extends InfrastructureException
{
    public static function forPayload(
        string $exchange,
        string $routingKey,
        array $payload,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Failed to publish message to exchange {$exchange} with routing key {$routingKey}",
            code: 500,
            previous: $previous,
            context: [
                'broker'      => 'rabbitmq',
                'exchange'    => $exchange,
                'routing_key' => $routingKey,
                'payload'     => $payload,
            ]
        );
    }
}
