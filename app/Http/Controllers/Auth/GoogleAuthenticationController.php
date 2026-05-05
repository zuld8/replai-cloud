<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\InternalSetting;
use Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Drive as Google_Service_Drive;

use Illuminate\Http\Request;

class GoogleAuthenticationController extends Controller
{
    public function redirect()
    {
        $setting    = InternalSetting::first([
            'google_client_id',
            'google_client_secret',
            'google_redirect'
        ]);
        $client     = new Google_Client();
        $client->setClientId($setting->google_client_secret);
        $client->setClientSecret($setting->google_client_secret);
        $client->setRedirectUri(route('google.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            Google_Service_Drive::DRIVE_METADATA_READONLY
        ]);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        $setting    = InternalSetting::first([
            'google_client_id',
            'google_client_secret',
            'google_redirect'
        ]);

        $client     = new Google_Client();
        $client->setClientId($setting->google_client_secret);
        $client->setClientSecret($setting->google_client_secret);
        $client->setRedirectUri(route('google.callback'));

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return response()->json(['error' => $token['error']], 400);
        }

        // Simpan token ke DB user
        // $user = auth()->user();
        // $user->google_token = json_encode($token);
        // $user->save();

        return redirect('/integrasi/google-sheet'); // halaman pilih sheet
    }
}
