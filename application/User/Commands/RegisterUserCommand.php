<?php

declare(strict_types=1);

namespace Application\User\Commands;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $password,
    ) {
        //
    }
}
