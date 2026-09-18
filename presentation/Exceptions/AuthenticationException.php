<?php

namespace Presentation\Exceptions;

/**
 * Exception for authentication failures in presentation layer
 */
class AuthenticationException extends PresentationException
{
    /**
     * Create a new authentication exception instance.
     */
    public function __construct(string $message = 'Unauthenticated', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 401); // Unauthorized
    }
}
