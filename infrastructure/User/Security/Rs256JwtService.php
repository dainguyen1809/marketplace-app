<?php

declare(strict_types=1);

namespace Infrastructure\User\Security;

use Application\User\Contracts\TokenService;
use Application\User\DTOs\TokenPairDTO;
use Application\User\Exceptions\TokenReuseException;
use Domain\User\Entities\UserEntity;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\ValueObjects\UserId;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Infrastructure\Cache\RedisCache;

final readonly class Rs256JwtService implements TokenService
{
    private const REDIS_KEY = 'auth:refresh_token:';

    private const USED_REDIS_KEY = 'auth:used_token:';

    public function __construct(private UserRepositoryInterface $userRepository, private RedisCache $redisCache)
    {
        //
    }

    public function generateTokenPair(UserEntity $user): TokenPairDTO
    {
        $now = time();
        $accessTTL = config('jwt.access_ttl', 900);
        $refreshTTL = config('jwt.refresh_ttl', 604800);

        // create payload access_token
        $payload = [
            'iss'   => config('jwt.issuer', 'ecommerce-api'),
            'sub'   => $user->id()->value(),
            'email' => $user->email()->value(),
            'iat'   => $now,
            'exp'   => $now + $accessTTL,
            'jti'   => (string) Str::uuid(),
        ];

        // sign access_token with private_key
        $privateKey = $this->getPrivateKey();
        $accessToken = JWT::encode($payload, $privateKey, 'RS256');

        // create random refresh_token and stores it in Redis
        $refreshToken = Str::random(64);
        $redisKey = self::REDIS_KEY.$refreshToken;
        $this->redisCache->set($redisKey, $user->id()->value(), $refreshTTL);

        return new TokenPairDTO(
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            expiresIn: $accessTTL,
            tokenType: 'Bearer'
        );
    }

    public function verifyAccessToken(string $token): array
    {
        $publicKey = $this->getPublicKey();

        // decoded and check signature with public_key
        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

        return (array) $decoded;
    }

    public function refresh(string $token): TokenPairDTO
    {
        $redisKey = self::REDIS_KEY.$token;

        $userId = $this->redisCache->get($redisKey);

        if (! $userId) {

            if ($this->redisCache->exists(self::USED_REDIS_KEY.$token)) {
                throw new TokenReuseException();
            }

            throw new Exception('Token invalid');
        }

        // refresh_token rotation (RTR)
        $this->redisCache->del($redisKey);

        $this->redisCache->set(self::USED_REDIS_KEY.$token, (string) $userId, 86400);

        $user = $this->userRepository->findById(UserId::fromString($userId));
        if (! $user) {
            throw new Exception('User not found!');
        }

        return $this->generateTokenPair($user);
    }

    public function revoke(string $refreshToken): void
    {
        $this->redisCache->del(self::REDIS_KEY.$refreshToken);
    }

    private function getPrivateKey(): string
    {
        $path = base_path((string) config('jwt.private_key_path'));
        if (! file_exists($path)) {
            throw new Exception("JWT Private Key not found at: {$path}");
        }

        return (string) file_get_contents($path);
    }

    private function getPublicKey(): string
    {
        $path = base_path((string) config('jwt.public_key_path'));
        if (! file_exists($path)) {
            throw new Exception("JWT Public Key not found at: {$path}");
        }

        return (string) file_get_contents($path);
    }
}
