<?php

namespace Presentation\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure consistent JSON response formatting
 */
class ResponseFormatterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $response = $next($request);

        // If response is already a JsonResponse, return as-is
        if ($response instanceof JsonResponse) {
            return $response;
        }

        // If response has JSON content, convert to proper JsonResponse
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Check if it already follows our format
            if (isset($decoded['success'])) {
                // Already formatted correctly
                return $response;
            }

            // Wrap in our standard format
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data'    => $decoded,
            ], $response->getStatusCode(), $response->headers->all());
        }

        // Return original response if not JSON or not decodable
        return $response;
    }
}
