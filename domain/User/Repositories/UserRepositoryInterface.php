<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Domain\User\Entities\UserEntity;
use Domain\User\ValueObjects\Email;
use Domain\User\ValueObjects\PhoneNumber;
use Domain\User\ValueObjects\UserId;

interface UserRepositoryInterface
{
    /**
     * Save the user info
     *
     * @param  UserEntity  $user
     * @return void
     */
    public function save(UserEntity $user): void;

    /**
     * Find user by ID
     *
     * @param  UserId  $id
     * @return UserEntity|null
     */
    public function findById(UserId $id): ?UserEntity;

    /**
     * Find user by Email
     *
     * @param  Email  $email
     * @return UserEntity|null
     */
    public function findByEmail(Email $email): ?UserEntity;

    /**
     * Find user by phone numbers
     *
     * @param  PhoneNumber  $phone
     * @return UserEntity|null
     */
    public function findByPhone(PhoneNumber $phone): ?UserEntity;
}
