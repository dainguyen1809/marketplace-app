<?php

namespace Presentation\Exceptions;

/**
 * Exception for authorization failures in presentation layer
 */
class AuthorizationException extends PresentationException
{
    /**
     * Create a new authorization exception instance.
     */
    public function __construct(string $message = "Unauthorized", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 403); // Forbidden
    }
}
