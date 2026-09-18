<?php

declare(strict_types=1);

namespace Application\User\Commands;

final readonly class LoginUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
        //
    }
}
