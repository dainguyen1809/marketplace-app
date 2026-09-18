<?php

declare(strict_types=1);

namespace Infrastructure\Cache;

use Closure;
use Exception;
use Illuminate\Support\Facades\Redis as RedisFacade;

/**
 * Simple wrapper for Laravel Redis facade with automatic JSON serialization/deserialization
 *
 * This class provides a convenient interface for caching data in Redis with automatic
 * handling of JSON encoding/decoding for complex data types. All operations are wrapped
 * in try/catch blocks to prevent exceptions from propagating, returning default values
 * on failure instead.
 */
class RedisCache
{
    /**
     * Get a value from Redis and attempt to decode it as JSON
     *
     * @param  string  $key  The cache key to retrieve
     * @param  mixed  $default  Default value to return if key doesn't exist or on error
     * @return mixed The decoded value (if JSON) or raw string, or $default on failure
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = RedisFacade::get($key);

            if ($value === null || $value === false) {
                return $default;
            }

            $decoded = json_decode((string) $value, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } catch (Exception) {
            return $default;
        }
    }

    /**
     * Store a value in Redis with optional TTL, automatically JSON encoding complex types
     *
     * @param  string  $key  The cache key to store the value under
     * @param  mixed  $value  The value to store (arrays/objects will be JSON encoded)
     * @param  int|null  $ttl  Time to live in seconds (null for no expiration)
     * @return bool True on success, false on failure
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            $payload = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

            if ($ttl !== null && $ttl > 0) {
                return (bool) RedisFacade::setex($key, $ttl, $payload);
            }

            return (bool) RedisFacade::set($key, $payload);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Delete one or more keys from Redis
     *
     * @param  string  $keys  One or more keys to delete
     * @return int Number of keys that were deleted
     */
    public function del(string ...$keys): int
    {
        try {
            return (int) RedisFacade::del(...$keys);
        } catch (Exception) {
            return 0;
        }
    }

    /**
     * Check if a key exists in Redis
     *
     * @param  string  $key  The key to check for existence
     * @return bool True if the key exists, false otherwise
     */
    public function exists(string $key): bool
    {
        try {
            return (bool) RedisFacade::exists($key);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Retrieve a value from cache, or execute a callback and store the result if not found
     *
     * This method implements the "cache-aside" pattern: it first attempts to get the value
     * from Redis. If found, it returns the cached value. If not found, it executes the
     * provided callback, stores the result in Redis with the specified TTL, and returns it.
     *
     * @param  string  $key  The cache key to check and store
     * @param  int  $ttl  Time to live in seconds for the cached value
     * @param  Closure  $callback  Function to execute if key is not found in cache
     * @return mixed The cached value or the result of the callback
     */
    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $freshValue = $callback();

        if ($freshValue !== null) {
            $this->set($key, $freshValue, $ttl);
        }

        return $freshValue;
    }

    /**
     * Increment the value of a key in Redis by a specified amount
     *
     * @param  string  $key  The key to increment
     * @param  int  $by  The amount to increment by (default: 1)
     * @return int The new value of the key after incrementing
     */
    public function incr(string $key, int $by = 1): int
    {
        return (int) RedisFacade::incrBy($key, $by);
    }

    /**
     * Decrement the value of a key in Redis by a specified amount
     *
     * @param  string  $key  The key to decrement
     * @param  int  $by  The amount to decrement by (default: 1)
     * @return int The new value of the key after decrementing
     */
    public function decr(string $key, int $by = 1): int
    {
        return (int) RedisFacade::decrBy($key, $by);
    }
}
