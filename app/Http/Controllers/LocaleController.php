<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request, string $locale): RedirectResponse
    {
        $locales = config('app.available_locales');

        abort_unless(is_array($locales) && array_key_exists($locale, $locales), 404);

        $request->session()->put('locale', $locale);

        return back();
    }
}
