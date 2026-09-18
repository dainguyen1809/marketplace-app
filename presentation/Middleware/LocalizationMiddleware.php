<?php

namespace Presentation\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

/**
 * Middleware to set application locale based on request
 */
class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from Accept-Language header, query parameter, or default
        $locale = $this->getLocaleFromRequest($request);

        // Set the application locale
        if ($locale && in_array($locale, config('app.available_locales', ['en', 'vi', 'zh', 'fr']))) {
            App::setLocale($locale);
            Lang::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Get locale from request.
     */
    protected function getLocaleFromRequest(Request $request): ?string
    {
        // Check query parameter first
        if ($request->has('locale') && $request->get('locale')) {
            return $request->get('locale');
        }

        // Check Accept-Language header
        if ($request->header('Accept-Language')) {
            $languages = explode(',', $request->header('Accept-Language'));
            foreach ($languages as $language) {
                $lang = explode(';', trim($language))[0]; // Remove quality factor
                $lang = strtolower(substr($lang, 0, 2)); // Get first 2 chars

                if (in_array($lang, config('app.available_locales', ['en', 'vi', 'zh', 'fr']))) {
                    return $lang;
                }
            }
        }

        // Return default locale
        return config('app.fallback_locale', 'en');
    }
}
