<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Ramsey\Uuid\Uuid;

class SettingsController extends Controller
{

    private $supportedLanguages = ['id', 'en', 'hi', 'pt', 'es', 'de', 'ar', 'ja', 'nl'];

    public function index()
    {
        $setting    = Setting::first();

        $courierStatus  = true;

        if ($setting->merchant_id != null && $setting->package_active) {
            if ($setting->package_active->cek_ongkir == 'no') {
                $courierStatus  = false;
            }
        }

        return view('settings', ['page'  => __('page.configuration'), 'breadcumb' => true], compact('setting', 'courierStatus'));
    }

    public function updateOnlineOffline()
    {
        $setting    = Setting::first(['id', 'is_online']);

        $setting->update([
            'is_online'     => $setting->is_online == 'yes' ? 'no' : 'yes'
        ]);

        return redirect()->back()->with(['flash'    => 'Berhasil memperbaharui Status Online']);
    }

    public function updateConfiguration(Request $request)
    {
        Setting::first()->update([
            'gmap_key'              => $request->gmap_key,
            'whatsapp_sender_notif' => $request->whatsapp_sender_notif,
            'mail_host'             => $request->mail_host,
            'mail_port'             => $request->mail_port,
            'mail_username'         => $request->mail_username,
            'mail_password'         => $request->mail_password,
            'mail_from_address'     => $request->mail_from_address,
            'mail_encryption'       => $request->mail_encryption,
            'mail_from_name'        => $request->mail_from_name,
            'timezone'              => $request->timezone,
            'scrapp_phone'          => $request->scrapp_phone,
            'scrapp_phone_whatsapp' => $request->scrapp_phone_whatsapp,
            'phone_country_code'    => $request->phone_country_code,
            'default_lang'          => $request->default_lang,
            'open_ai_key'           => $request->open_ai_key,
            'api_device_use'        => $request->api_device_use,
            'ai_option'             => $request->ai_option,
            'google_text_to_audio'  => $request->google_text_to_audio,
            'history_ai_chat_option'    => $request->history_ai_chat_option,
            'history_ai_chat'           => $request->history_ai_chat_option == 'yes' ? $request->history_ai_chat : 0,
            'cek_ongkir_api'        => isset($request->cek_ongkir_api) ? $request->cek_ongkir_api : null,
            'signature_option'      => $request->signature_option,
            'signature_text'        => $request->signature_text
        ]);

        return redirect()->back()->with(['flash'    => __('general.success_update')]);
    }

    public function setLang(String $lang)
    {

        if (!in_array($lang, $this->supportedLanguages)) {
            abort(404, 'Language not supported');
        }

        session(['locale' => $lang]);
        app()->setLocale($lang);
        Config::set('app.locale', $lang);
 
        return redirect()->back()->with('success', __('messages.language_changed'));
    }

    public function generateApiKey()
    {

        $newApiKey      = Uuid::uuid4()->toString();
        Setting::first()->update([
            'local_api_key'     => $newApiKey,
        ]);

        return response()->json([
            'message'  => $newApiKey,
        ]);
    }
}
