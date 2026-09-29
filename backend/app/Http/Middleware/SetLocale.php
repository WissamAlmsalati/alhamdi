<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin panel language. Arabic is the default; the choice is kept in the session.
 */
class SetLocale
{
    public const SUPPORTED = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', 'ar');

        app()->setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : 'ar');

        return $next($request);
    }
}
