<?php

namespace Presentation\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Middleware to log incoming requests and outgoing responses
 */
class RequestLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log request information
        $requestData = $request->all();

        // Remove sensitive data from logs
        $sanitizedData = $this->sanitizeRequestData($requestData);

        Log::info('Incoming request', [
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
            'headers' => $request->headers->all(),
            'data'    => $sanitizedData,
        ]);

        // Process the request
        $response = $next($request);

        // Log response information
        Log::info('Outgoing response', [
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'status'  => $response->getStatusCode(),
            'headers' => $response->headers->all(),
        ]);

        return $response;
    }

    /**
     * Sanitize request data by removing sensitive fields.
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'secret', 'api_key', 'credit_card'];

        return array_map(function ($value, $key) use ($sensitiveKeys) {
            if (is_array($value)) {
                return $this->sanitizeRequestData($value);
            }

            if (is_string($key) && in_array(strtolower($key), $sensitiveKeys)) {
                return '***REDACTED***';
            }

            return $value;
        }, $data, array_keys($data));
    }
}
