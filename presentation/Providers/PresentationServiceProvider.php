<?php

namespace Presentation\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Presentation\Exceptions\PresentationException;
use Presentation\Middleware\JwtAuthMiddleware;
use Presentation\Middleware\LocalizationMiddleware;
use Presentation\Middleware\RequestLoggingMiddleware;
use Presentation\Middleware\ResponseFormatterMiddleware;
use Presentation\Middleware\ValidateApiKeyMiddleware;
use Presentation\Shared\Helpers\RequestHelper;
use Presentation\Shared\Helpers\ResponseHelper;

/**
 * Presentation layer service provider
 */
class PresentationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register shared helpers as singletons
        $this->app->singleton('responseHelper', function () {
            return new ResponseHelper();
        });

        $this->app->singleton('requestHelper', function () {
            return new RequestHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register middleware
        $this->app['router']->aliasMiddleware(
            'presentation.request.log', RequestLoggingMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'presentation.api_key', ValidateApiKeyMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'presentation.response.format', ResponseFormatterMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'presentation.localization', LocalizationMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'auth.jwt', JwtAuthMiddleware::class
        );

        // Register exception handler for presentation exceptions
        $this->app->bind(PresentationException::class, function ($app) {
            return function ($exception) {
                // Log the exception if it should be logged
                if ($exception->shouldLog()) {
                    Log::error($exception->getMessage(), [
                        'exception' => get_class($exception),
                        'code'      => $exception->getCode(),
                        'trace'     => $exception->getTraceAsString(),
                    ]);
                }

                // Return JSON response for API requests
                if (request()->is('api/*') || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'errors'  => null,
                    ], $exception->getStatusCode());
                }

                // For non-API requests, let Laravel handle it normally
                return $exception;
            };
        });
    }
}
