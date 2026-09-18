<?php

declare(strict_types=1);

namespace Application\User\Contracts;

interface PasswordHasher
{
    /**
     * Hash a raw password to a secure hash string
     *
     * @param  string  $rawPassword
     * @return string
     */
    public function hash(string $rawPassword): string;

    /**
     * Check whether the raw password matches the hash string
     * @param string $rawPassword
     * @param string $hashedPassword
     * @return bool
     */
    public function verify(string $rawPassword, string $hashedPassword): bool;
}
