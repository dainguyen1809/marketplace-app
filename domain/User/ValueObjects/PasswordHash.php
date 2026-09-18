<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;

final class PasswordHash
{
    private function __construct(private readonly string $value) {}

    public static function fromHash(string $hash): self
    {
        if ($hash === '' || password_get_info($hash)['algo'] === 0) {
            throw new InvalidArgumentException('Invalid password hash.');
        }

        return new self($hash);
    }

    public function value(): string
    {
        return $this->value;
    }
}
