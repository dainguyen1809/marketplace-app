<?php

declare(strict_types=1);

namespace Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Presentation\Shared\Helpers\LoggingHelper;
use Presentation\Shared\Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, int $maxAttempts, $time = 1): Response
    {
        $key = 'rate_limit:'.($request->ip()) ?? 'unknown';

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            LoggingHelper::rateLimit($request);

            return ResponseHelper::error(
                message: 'BAD_GATEWAY',
                code: Response::HTTP_BAD_GATEWAY
            );
        }

        RateLimiter::hit($key, $time * 60);

        return $next($request);
    }
}
