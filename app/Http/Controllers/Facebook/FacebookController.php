<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function index(Request $request)
    {
        return view('facebook.index',['page'    => __('platform.facebook.account_list')]);   
    }

    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes(['pages_show_list', 'pages_messaging', 'instagram_basic', 'pages_read_engagement', 'instagram_manage_messages'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();

            // Simpan token dan ID pengguna Facebook
            $accessToken = $fbUser->token;
            $facebookId = $fbUser->getId();
            $name = $fbUser->getName();

            // Request page & IG linked data (opsional untuk integrasi lebih lanjut)
            $pages = Http::get("https://graph.facebook.com/v22.0/me/accounts", [
                'access_token' => $accessToken
            ])->json();

            // Simpan ke DB sesuai kebutuhan aplikasi kamu
            // Contoh:
            // UserFacebook::updateOrCreate([...], [...]);

            return redirect()->route('dashboard')->with('success', '' . __('platform.facebook.account_connected'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', '' . __('platform.facebook.login_failed') . $e->getMessage());
        }
    }
    
}
