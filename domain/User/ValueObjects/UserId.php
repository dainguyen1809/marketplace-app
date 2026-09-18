<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;

final class UserId
{
    private function __construct(private readonly string $value)
    {
        //
    }

    public static function fromString(string $id): self
    {
        if ($id === '' || ! is_string($id)) {
            throw new InvalidArgumentException('User ID must be a non-empty string.');
        }

        return new self($id);
    }

    public function value(): string
    {
        return $this->value;
    }
}
