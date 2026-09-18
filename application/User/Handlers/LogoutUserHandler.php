<?php

declare(strict_types=1);

namespace Application\User\Handlers;

use Application\User\Contracts\TokenService;

final readonly class LogoutUserHandler
{
    public function __construct(private TokenService $tokenService)
    {
        //
    }

    public function handle(string $refreshToken): void
    {
        $this->tokenService->revoke($refreshToken);
    }
}
