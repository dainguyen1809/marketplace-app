<?php

declare(strict_types=1);

namespace Infrastructure\User\Security;

use Application\User\Contracts\PasswordHasher;
use Illuminate\Support\Facades\Hash;

final class LaravelPasswordHasher implements PasswordHasher {
    public function hash(string $rawPassword): string {
        return Hash::make($rawPassword);
    }

    public function verify(string $rawPassword, string $hashedPassword): bool {
        return Hash::check($rawPassword, $hashedPassword);
    }

}
