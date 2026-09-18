<?php

declare(strict_types=1);

namespace Application\User\Handlers;

use Application\User\Contracts\TokenService;
use Application\User\DTOs\TokenPairDTO;

final readonly class RefreshTokenHandler
{
    public function __construct(private TokenService $tokenService)
    {
        //
    }

    public function handle(string $refreshToken): TokenPairDTO
    {
        return $this->tokenService->refresh($refreshToken);
    }
}
