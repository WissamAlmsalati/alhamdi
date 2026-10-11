<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin panel language. Arabic is the default; the choice is kept in the session.
 * Switching uses "?lang=en" on the current page so it stays on a path the site proxy
 * already sends to Laravel (a separate /locale route would land on the React site).
 */
class SetLocale
{
    public const SUPPORTED = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, self::SUPPORTED, true)) {
            $request->session()->put('locale', $requested);

            if ($request->isMethod('GET')) {
                // Relative redirect back to the same page without "lang", so it keeps the visitor's host and scheme.
                $query = http_build_query($request->except('lang'));

                return new RedirectResponse($request->getBaseUrl().$request->getPathInfo().($query !== '' ? '?'.$query : ''));
            }
        }

        $locale = $request->session()->get('locale', 'ar');

        app()->setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : 'ar');

        return $next($request);
    }
}
