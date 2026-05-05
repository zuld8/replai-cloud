<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\InternalSetting;
use App\Models\Setting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        $internalSetting = InternalSetting::first(['logo', 'register', 'app_name', 'white_logo']);
        return view('auth.login', ['page' => __('page.login')], compact('internalSetting'));
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        $emailSettings = Setting::withoutGlobalScopes()->where("merchant_id", null)->first(['timezone', 'default_lang']);
        $userSetting = null;

        if (check_user() && my_user()->role == 'user') {
            $userSetting = Setting::withoutGlobalScopes()->where('merchant_id', my_user()->merchant_id)->first(['timezone', 'default_lang']);
        }

        Config::set('app.timezone', $userSetting->timezone ?? $emailSettings->timezone ?? config('app.timezone'));

        $locale = $userSetting->default_lang ?? $emailSettings->default_lang ?? config('app.locale', 'en');
 
        session(['locale' => $locale]);

        Config::set('app.locale', $locale);
        app()->setLocale($locale); 

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('home');
    }
 
}