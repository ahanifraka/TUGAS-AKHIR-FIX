<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Determine locale from route parameter or persistent cookie
        $routeLocale = $request->route('locale');
        $cookieLocale = $request->cookie('locale');
        $locale = $routeLocale ?: $cookieLocale;

        // Accept only supported locales; otherwise fallback to configured default
        if (!in_array($locale, ['id', 'en'], true)) {
            $locale = config('app.default_locale', 'id');
        }

        // Apply locale to Laravel translator and Carbon
        app()->setLocale($locale);
        Carbon::setLocale($locale);

        // Persist locale in an encrypted cookie (1 year) so it survives SPA and full reloads
        if ($cookieLocale !== $locale) {
            Cookie::queue('locale', $locale, 60 * 24 * 365, '/');
        }

        return $next($request);
    }
}
