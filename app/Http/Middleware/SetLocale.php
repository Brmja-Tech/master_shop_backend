<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->header('lang') 
            ?? $request->input('lang') 
            ?? 'ar';

        // Normalize locale (e.g. if 'en-US' is sent, use 'en')
        $locale = substr(strtolower($locale), 0, 2);

        if (! in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
