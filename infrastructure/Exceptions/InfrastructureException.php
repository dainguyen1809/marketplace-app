<?php

declare(strict_types=1);

namespace Infrastructure\Exceptions;

use RuntimeException;
use Throwable;

abstract class InfrastructureException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        protected array $context = []
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function context(): array
    {
        return $this->context;
    }
}
