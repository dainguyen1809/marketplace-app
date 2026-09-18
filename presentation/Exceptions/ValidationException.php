<?php

namespace Presentation\Exceptions;

/**
 * Exception for validation failures in presentation layer
 */
class ValidationException extends PresentationException
{
    /**
     * Validation errors
     */
    protected array $errors = [];

    /**
     * Create a new validation exception instance.
     */
    public function __construct(string $message = 'Validation failed', array $errors = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 422); // Unprocessable Entity
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set the validation errors.
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }
}
