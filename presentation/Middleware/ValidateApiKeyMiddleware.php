<?php

declare(strict_types=1);

namespace Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Presentation\Shared\Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedApiKey = env('API_SECRET_KEY');

        $providedApiKey = $request->header('X-API-KEY');

        if (! $expectedApiKey || ! $providedApiKey || ! hash_equals($expectedApiKey, $providedApiKey)) {
            return ResponseHelper::error(
                message: 'BAD REQUEST',
                code: Response::HTTP_BAD_REQUEST
            );
        }

        return $next($request);
    }
}
