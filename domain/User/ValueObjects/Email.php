<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;

final class Email
{
    private function __construct(private readonly string $value)
    {
        //
    }

    public static function from(string $value): self
    {
        $normalizedValue = strtolower(trim($value));

        if ($normalizedValue === '' || filter_var($normalizedValue, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        return new self($normalizedValue);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
