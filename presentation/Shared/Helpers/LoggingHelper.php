<?php

declare(strict_types=1);

namespace Presentation\Shared\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoggingHelper
{
    public static function rateLimit(Request $request, string $message = 'Rate limit exceeded', array $extra = []): void
    {
        Log::channel('rate_limit')->warning($message, array_merge([
            'ip'         => $request->ip() ?? 'unknown',
            'endpoint'   => $request->path(),
            'method'     => $request->method(),
            'user_agent' => $request->userAgent(),
            'time'       => now()->toIso8601String(),
        ], $extra));
    }

    public static function tokenReuse(Request $request, string $message = 'Security Alert: Refresh token reuse detected!', array $extra = []): void
    {
        Log::channel('auth')->alert($message, array_merge([
            'ip'         => $request->ip() ?? 'unknown',
            'endpoint'   => $request->path(),
            'method'     => $request->method(),
            'user_agent' => $request->userAgent(),
            'time'       => now()->toIso8601String(),
        ], $extra));
    }
}
