<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the active application locale.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, SetLocaleMiddleware::SUPPORTED_LOCALES, true)) {
            session(['locale' => $locale]);
            cookie()->queue(cookie()->forever('locale', $locale));
        }

        $referer = $request->header('referer');
        if ($referer && filter_var($referer, FILTER_VALIDATE_URL)) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $appHost     = $request->getHost();
            if ($refererHost === $appHost) {
                return redirect()->to($referer);
            }
        }

        return redirect()->route('home');
    }
}
