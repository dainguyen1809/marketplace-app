<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;

final class PhoneNumber
{
    private function __construct(private readonly string $value)
    {
        //
    }

    private const PATTERN = '/^\+[1-9]\d{1,14}$/';

    public static function from(string $phone): self
    {
        $normalized = trim($phone);

        if ($normalized === '' || preg_match(self::PATTERN, $normalized) !== 1) {
            throw new InvalidArgumentException('Invalid phone number');
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
