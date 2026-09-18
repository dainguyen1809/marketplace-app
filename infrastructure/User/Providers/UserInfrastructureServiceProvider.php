<?php

declare(strict_types=1);

namespace Infrastructure\User\Providers;

use Application\User\Contracts\PasswordHasher;
use Application\User\Contracts\TokenService;
use Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\User\Repositories\EloquentUserRepository;
use Infrastructure\User\Security\LaravelPasswordHasher;
use Infrastructure\User\Security\Rs256JwtService;

final class UserInfrastructureServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => EloquentUserRepository::class,
        PasswordHasher::class          => LaravelPasswordHasher::class,
        TokenService::class            => Rs256JwtService::class,
    ];
}
