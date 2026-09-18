<?php

declare(strict_types=1);

namespace Presentation\Middleware;

use Application\User\Contracts\TokenService;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Presentation\Shared\Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JwtAuthMiddleware
{
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return ResponseHelper::error(
                message: 'Unauthorized',
                code: Response::HTTP_UNAUTHORIZED
            );
        }

        try {
            $claims = $this->tokenService->verifyAccessToken($token);

            $request->attributes->set('auth_user_id', (string) $claims['sub']);
            $request->attributes->set('auth_user_email', (string) ($claims['email'] ?? ''));

            return $next($request);

        } catch (ExpiredException $e) {
            $this->logSecurityWarning($request, 'JWT token expired', $e);

            return ResponseHelper::error(
                message: 'Token has expired',
                code: Response::HTTP_UNAUTHORIZED
            );

        } catch (SignatureInvalidException $e) {
            $this->logSecurityWarning($request, 'Invalid JWT token signature detected', $e);

            return ResponseHelper::error(
                message: 'Unauthorized',
                code: Response::HTTP_UNAUTHORIZED
            );

        } catch (Throwable $e) {
            $this->logSecurityWarning($request, 'JWT verification failed: '.$e->getMessage(), $e);

            return ResponseHelper::error(
                message: 'Unauthorized',
                code: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    private function logSecurityWarning(Request $request, string $reason, Throwable $exception): void
    {
        Log::channel('auth')->warning($reason, [
            'ip'         => $request->ip() ?? 'unknown',
            'endpoint'   => $request->path(),
            'method'     => $request->method(),
            'user_agent' => $request->userAgent(),
            'exception'  => get_class($exception),
            'time'       => now()->toIso8601String(),
        ]);
    }
}
