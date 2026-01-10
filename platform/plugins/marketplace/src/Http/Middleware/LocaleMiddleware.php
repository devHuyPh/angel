<?php

namespace Botble\Marketplace\Http\Middleware;

use Botble\Base\Supports\Language;
use Botble\Ecommerce\Models\Customer;
use Botble\Language\Models\Language as ModelsLanguage;
use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        
        $language = ModelsLanguage::where('lang_locale', session('language'))->first();
        
        $currentLocale = $language ? [
            'locale' => $language->lang_locale,
            'code' => $language->lang_code,
            'name' => $language->lang_name,
            'flag' => $language->lang_flag,
            'is_rtl' => (bool) $language->lang_is_rtl,
        ] : Language::getDefaultLanguage();
        
        // dd($currentLocale);

        /**
         * @var Customer $account
         */
        $account = auth('customer')->user();

        if (!$account) {
            return redirect()->route('customer.login');
        }


        $userLocale = $account->getMetaData('locale', true);

        if ($userLocale && array_key_exists($userLocale, $availableLocales = Language::getAvailableLocales())) {
            $currentLocale = $availableLocales[$userLocale];
        }

        if ($currentLocale && isset($currentLocale['locale'])) {
            app()->setLocale($currentLocale['locale']);
            $request->setLocale($currentLocale['locale']);
            $request->session()->put('locale_direction', $currentLocale['is_rtl']);
        }

        return $next($request);
    }
}
