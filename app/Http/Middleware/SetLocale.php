<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = strtolower(substr($request->header('Accept-Language', 'en'), 0, 2));

        if (! in_array($locale, ['ar', 'en'])) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
