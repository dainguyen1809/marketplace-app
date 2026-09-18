<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Application\User\DTOs\TokenPairDTO;
use Domain\User\Entities\UserEntity;

interface TokenService
{
    /**
     * Create pair access_token and refresh_token
     *
     * @param  UserEntity  $user
     * @return TokenPairDTO
     */
    public function generateTokenPair(UserEntity $user): TokenPairDTO;

    /**
     * Verify the access_token and return to claims
     *
     * @param  string  $token
     * @return array
     */
    public function verifyAccessToken(string $token): array;

    /**
     * Refresh the access token using the refresh token
     *
     * @param  string  $token
     * @return TokenPairDTO
     */
    public function refresh(string $token): TokenPairDTO;

    /**
     * Revoke the refresh token
     *
     * @param  string  $refreshToken
     */
    public function revoke(string $refreshToken): void;
}
