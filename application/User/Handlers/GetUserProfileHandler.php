<?php

declare(strict_types=1);

namespace Application\User\Handlers;

use Application\User\DTOs\UserProfileDTO;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\ValueObjects\UserId;
use Exception;

final readonly class GetUserProfileHandler
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
        //
    }

    public function handle(string $userId): UserProfileDTO
    {
        $id = UserId::fromString($userId);
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new Exception('User not found');
        }

        return UserProfileDTO::fromEntity($user);
    }
}
