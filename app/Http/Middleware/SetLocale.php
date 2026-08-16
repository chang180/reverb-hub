<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', (string) config('app.locale'));

        $locales = config('app.available_locales');

        if (is_string($locale) && is_array($locales) && array_key_exists($locale, $locales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
