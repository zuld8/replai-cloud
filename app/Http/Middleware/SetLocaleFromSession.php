<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!session()->has('locale')) {
            $emailSettings  = Setting::withoutGlobalScopes()->where("merchant_id", null)->first(['timezone', 'default_lang']);
            $userSetting    = null;

            if (check_user()) {
                $userSetting = Setting::withoutGlobalScopes()->where('merchant_id', my_user()->merchant_id)->first(['timezone', 'default_lang']);
            }

            Config::set('app.timezone', $userSetting->timezone ?? $emailSettings->timezone ?? config('app.timezone'));

            $locale = session('locale') ?? $userSetting->default_lang ?? $emailSettings->default_lang ?? config('app.locale', 'en');

            if (!session()->has('locale')) {
                session(['locale' => $locale]);
            }

            Config::set('app.locale', $locale);
            app()->setLocale($locale);
        } else {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
