<?php

namespace Presentation\Shared\Helpers;

use Illuminate\Http\Request;

/**
 * Helper class for working with HTTP requests
 */
class RequestHelper
{
    /**
     * Get the authenticated user ID from request (if available).
     * Note: This is a placeholder - actual auth logic would be in User module which we're skipping.
     */
    public static function getUserId(Request $request): ?int
    {
        // This would normally integrate with auth system
        // Since we're skipping User module, returning null as placeholder
        return null;
    }

    /**
     * Check if request has valid JSON content.
     */
    public static function isJson(Request $request): bool
    {
        return $request->isJson() || $request->headers->get('Content-Type') === 'application/json';
    }

    /**
     * Get JSON data from request.
     */
    public static function getJson(Request $request): array
    {
        return $request->json()->all();
    }

    /**
     * Get header value from request.
     */
    public static function getHeader(Request $request, string $key, string $default = ''): string
    {
        return $request->header($key, $default);
    }

    /**
     * Get query parameter from request.
     *
     * @return mixed
     */
    public static function getQueryParam(Request $request, string $key, mixed $default = null)
    {
        return $request->query($key, $default);
    }
}
