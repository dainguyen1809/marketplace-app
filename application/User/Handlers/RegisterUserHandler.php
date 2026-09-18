<?php

declare(strict_types=1);

namespace Application\User\Handlers;

use Application\User\Commands\RegisterUserCommand;
use Application\User\Contracts\PasswordHasher;
use Domain\User\Entities\UserEntity;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\ValueObjects\Email;
use Domain\User\ValueObjects\PasswordHash;
use Domain\User\ValueObjects\PhoneNumber;
use Domain\User\ValueObjects\UserId;
use Illuminate\Support\Str;

final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasher $hasher
    ) {
        //
    }

    public function handle(RegisterUserCommand $command): UserId
    {
        $userId = UserId::fromString((string) Str::uuid());
        $email = Email::from($command->email);
        $name = $command->name;
        $phone = PhoneNumber::from($command->phone);
        $hashedPassword = $this->hasher->hash($command->password);
        $passwordHash = PasswordHash::fromHash($hashedPassword);

        // init domain entity
        $user = UserEntity::register($userId, $name, $email, $phone, $passwordHash);

        $this->userRepository->save($user);

        return $user->id();
    }
}
