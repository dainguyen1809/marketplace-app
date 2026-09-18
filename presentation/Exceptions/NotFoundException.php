<?php

namespace Presentation\Exceptions;

/**
 * Exception for resource not found in presentation layer
 */
class NotFoundException extends PresentationException
{
    /**
     * Create a new not found exception instance.
     */
    public function __construct(string $message = 'Resource not found', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 404); // Not Found
    }
}
