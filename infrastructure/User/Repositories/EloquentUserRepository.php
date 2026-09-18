<?php

declare(strict_types=1);

namespace Infrastructure\User\Repositories;

use Domain\User\Entities\UserEntity;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\ValueObjects\Email;
use Domain\User\ValueObjects\PasswordHash;
use Domain\User\ValueObjects\PhoneNumber;
use Domain\User\ValueObjects\UserId;
use Infrastructure\User\Models\UserModel;

final readonly class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserModel $model
    ) {
        //
    }

    public function save(UserEntity $user): void
    {
        $this->model->newQuery()->updateOrCreate(
            [
                'uuid' => $user->id()->value(),
            ],
            [
                'name'          => $user->name(),
                'email'         => $user->email()->value(),
                'phone_numbers' => $user->phone()->value(),
                'password'      => $user->passwordHash()->value(),
            ]
        );
    }

    public function findById(UserId $id): ?UserEntity
    {
        $record = $this->model->newQuery()->where('uuid', $id->value())->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findByEmail(Email $email): ?UserEntity
    {
        $record = $this->model->newQuery()->where('email', $email->value())->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findByPhone(PhoneNumber $phone): ?UserEntity
    {
        $record = $this->model->newQuery()->where('phone_numbers', $phone->value())->first();

        return $record ? $this->toEntity($record) : null;
    }

    private function toEntity(UserModel $model): UserEntity
    {
        return UserEntity::register(
            UserId::fromString($model->uuid),
            $model->name,
            Email::from($model->email),
            PhoneNumber::from($model->phone_numbers),
            PasswordHash::fromHash($model->password)
        );
    }
}
