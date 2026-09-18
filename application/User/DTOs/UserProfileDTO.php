<?php

declare(strict_types=1);

namespace Application\User\DTOs;

use Domain\User\Entities\UserEntity;

final readonly class UserProfileDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $phone,
    ) {
        //
    }

    public static function fromEntity(UserEntity $user): self
    {
        return new self(
            id: $user->id()->value(),
            name: $user->name(),
            email: $user->email()->value(),
            phone: $user->phone()->value(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
