<?php

namespace Presentation\Exceptions;

use Exception;

/**
 * Base exception class for presentation layer
 */
class PresentationException extends Exception
{
    /**
     * HTTP status code for the exception
     */
    protected int $statusCode = 500;

    /**
     * Whether this exception should be logged
     */
    protected bool $shouldLog = true;

    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, int $statusCode = 500)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set the HTTP status code.
     */
    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Determine if the exception should be logged.
     */
    public function shouldLog(): bool
    {
        return $this->shouldLog;
    }

    /**
     * Set whether the exception should be logged.
     */
    public function setShouldLog(bool $shouldLog): static
    {
        $this->shouldLog = $shouldLog;

        return $this;
    }
}
