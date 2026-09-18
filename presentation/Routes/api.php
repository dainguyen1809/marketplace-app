<?php

use Illuminate\Support\Facades\Route;
use Presentation\Middleware\RateLimitMiddleware;
use Presentation\Middleware\ValidateApiKeyMiddleware;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'healthy']));

    Route::middleware([
        ValidateApiKeyMiddleware::class,
        RateLimitMiddleware::class.':60,1',
    ])
        ->group(function () {
            Route::prefix('auth')->middleware(RateLimitMiddleware::class.':10,1')->group(base_path('presentation/User/Routes/auth.php'));
        });
});
