<?php

declare(strict_types=1);

namespace Application\User\Handlers;

use Application\User\Commands\LoginUserCommand;
use Application\User\Contracts\PasswordHasher;
use Application\User\Contracts\TokenService;
use Application\User\DTOs\TokenPairDTO;
use Application\User\Exceptions\InvalidCredentialsException;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\ValueObjects\Email;

/**
 * Handles user login requests
 *
 * This handler processes login commands by verifying user credentials
 * and generating authentication token pairs upon successful validation upon success.
 */
final readonly class LoginUserHandler
{
    /**
     * @param  UserRepositoryInterface  $userRepository  Repository for user data access
     * @param  PasswordHasher  $hasher  Service for password verification
     * @param  TokenService  $tokenService  Service for generating authentication tokens
     * @param  string  $tokenType  Service for type of tokens
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasher $hasher,
        private TokenService $tokenService,
        public string $tokenType = 'Bearer',
    ) {
        //
    }

    /**
     * Process the login command and generate token pair for valid credentials
     *
     * @param LoginUserCommand $command Contains user email and password for authentication
     * @return TokenPairDTO Authentication tokens (access and refresh) for the user
     * @throws Exception When credentials are invalid or user not found
     */
    public function handle(LoginUserCommand $command): TokenPairDTO
    {
        $email = Email::from($command->email);
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! $this->hasher->verify($command->password, $user->passwordHash()->value())) {
            throw new InvalidCredentialsException();
        }

        return $this->tokenService->generateTokenPair($user);
    }
}
