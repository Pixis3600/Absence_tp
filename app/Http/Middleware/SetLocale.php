<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('app_locale');

        if (! is_string($locale) || ! in_array($locale, ['fr', 'en'], true)) {
            $locale = $request->getPreferredLanguage(['fr', 'en']) ?? config('app.locale');
        }

        if (! in_array($locale, ['fr', 'en'], true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
