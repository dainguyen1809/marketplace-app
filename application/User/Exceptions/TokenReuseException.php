<?php

namespace Application\User\Exceptions;

use Exception;

class TokenReuseException extends Exception
{
    public function __construct(string $message = 'Refresh token reuse detected.')
    {
        parent::__construct($message);
    }
}
