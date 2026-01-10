<?php

namespace App\Http\Middleware;

use Botble\Base\Supports\Language;
use Botble\Language\Models\Language as ModelsLanguage;
use Closure;
use Illuminate\Http\Request;

class MarketingLocaleMiddleware
{
public function handle(Request $request, Closure $next)
{
    $customer = auth('customer')->user();

    if (!$customer){
        return redirect()->route('customer.login');
    }

    $availableLocales = Language::getAvailableLocales();

    $defaultLanguage = ModelsLanguage::where('lang_is_default', true)->first();
    $defaultLocale = $defaultLanguage->lang_locale ?? config('app.locale');

    $segments = $request->segments();
    $localeFromUrl = $segments[0] ?? null;

    if ($localeFromUrl && array_key_exists($localeFromUrl, $availableLocales)) {
        // Nếu là locale mặc định => redirect bỏ locale
        if ($localeFromUrl === $defaultLocale) {
            $newSegments = $segments;
            unset($newSegments[0]);
            $newPath = '/' . implode('/', $newSegments);

            // Redirect 301 để tránh duplicate SEO
            return redirect($newPath, 301);
        }

        $locale = $localeFromUrl;
    } else {
        // Nếu không có locale trong URL, dùng mặc định (không lấy từ session nữa)
        $locale = $defaultLocale;
    }

    // Set lại ngôn ngữ cho Laravel
    app()->setLocale($locale);
    $request->setLocale($locale);

    // Nếu cần thì mới lưu lại session
    session(['language' => $locale]);
    session(['locale_direction' => $availableLocales[$locale]['is_rtl'] ?? false]);

    return $next($request);
}

}
