<?php

declare(strict_types=1);

namespace Domain\User\Entities;

use Domain\User\ValueObjects\Email;
use Domain\User\ValueObjects\PasswordHash;
use Domain\User\ValueObjects\PhoneNumber;
use Domain\User\ValueObjects\UserId;
use InvalidArgumentException;

class UserEntity
{
    private function __construct(
        private readonly UserId $id,
        private readonly string $name,
        private readonly Email $email,
        private readonly PhoneNumber $phone,
        private readonly PasswordHash $hash,
    ) {
        //
    }

    /**
     * Summary of register
     *
     * @param  UserId  $id
     * @param  string  $name
     * @param  Email  $email
     * @param  PhoneNumber  $phone
     * @param  PasswordHash  $hash
     * @return UserEntity
     *
     * @throws InvalidArgumentException
     */
    public static function register(UserId $id, string $name, Email $email, PhoneNumber $phone, PasswordHash $hash): self
    {
        $trimmedName = trim($name);

        if ($trimmedName === '') {
            throw new InvalidArgumentException('Name cannot be empty.');
        }

        return new self($id, $trimmedName, $email, $phone, $hash);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function phone(): PhoneNumber
    {
        return $this->phone;
    }

    public function passwordHash(): PasswordHash
    {
        return $this->hash;
    }
}
