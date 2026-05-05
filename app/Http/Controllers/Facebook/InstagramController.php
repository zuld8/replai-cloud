<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\Meta\InstagramAccount;
use App\Observers\ChatBot\FineTunnelObserver;
use App\Observers\UserObserver;
use App\Services\Platform\InstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramController extends Controller
{

    protected $instagramService;
    protected $usersObserver;
    protected $fineTunnelObserver;

    protected $apiVersion = 'v22.0';
    protected $graphApiUrl = 'https://graph.facebook.com';

    public function __construct(InstagramService $instagramService, UserObserver $usersObserver, FineTunnelObserver $fineTunnelObserver)
    {
        $this->instagramService     = $instagramService;
        $this->usersObserver        = $usersObserver;
        $this->fineTunnelObserver   = $fineTunnelObserver;
    }

    /**
     * Display Instagram accounts list
     */
    public function index(Request $request)
    {
        $accounts   = $this->instagramService->getData($request)->get();
        return view('instagram.index', ['page'   => 'Akun Instagram'], compact('accounts'));
    }

    /**
     * Redirect to Instagram OAuth (Instagram API v20+)
     * Uses api.instagram.com - NOT Facebook OAuth dialog
     */
    public function redirectToInstagram()
    {
        $platform    = platform_currency();
        $appId       = $platform->fb_app_id;
        $redirectUri = route('instagram.redirect');

        $scopes = implode(',', [
            'instagram_business_basic',
            'instagram_business_manage_messages',
        ]);

        $url = 'https://api.instagram.com/oauth/authorize?' . http_build_query([
            'client_id'     => $appId,
            'redirect_uri'  => $redirectUri,
            'scope'         => $scopes,
            'response_type' => 'code',
        ]);

        return redirect($url);
    }

    /**
     * Handle OAuth callback from Facebook/Instagram
     */
    public function handleCallback(Request $request)
    {
        try {
            // AJAX POST with access_token
            $userAccessToken = $request->input('access_token');
            $authCode        = $request->input('code');

            // Exchange authorization code for access token
            // Detect source: state=fb_oauth → Facebook Graph API, else → Instagram OAuth
            if ($authCode && !$userAccessToken) {
                $platform    = platform_currency();
                $redirectUri = route('instagram.redirect');
                $source      = $request->input('state', '');

                if ($source === 'fb_oauth') {
                    // Code dari Facebook OAuth dialog — exchange via Graph API
                    $tokenResp = \Illuminate\Support\Facades\Http::get(
                        'https://graph.facebook.com/v22.0/oauth/access_token',
                        [
                            'client_id'     => $platform->fb_app_id,
                            'client_secret' => $platform->fb_app_secret,
                            'redirect_uri'  => $redirectUri,
                            'code'          => $authCode,
                        ]
                    );
                } elseif ($source === 'ig_business') {
                    // Code dari Instagram Business Login — exchange via api.instagram.com
                    $igAppId     = $platform->ig_app_id     ?? $platform->fb_app_id;
                    $igAppSecret = $platform->ig_app_secret ?? $platform->fb_app_secret;
                    $tokenResp = \Illuminate\Support\Facades\Http::asForm()->post(
                        'https://api.instagram.com/oauth/access_token',
                        [
                            'client_id'     => $igAppId,
                            'client_secret' => $igAppSecret,
                            'grant_type'    => 'authorization_code',
                            'redirect_uri'  => $redirectUri,
                            'code'          => $authCode,
                        ]
                    );
                } else {
                    // Code dari Instagram OAuth — exchange via api.instagram.com (legacy)
                    $tokenResp = \Illuminate\Support\Facades\Http::asForm()->post(
                        'https://api.instagram.com/oauth/access_token',
                        [
                            'client_id'     => $platform->fb_app_id,
                            'client_secret' => $platform->fb_app_secret,
                            'grant_type'    => 'authorization_code',
                            'redirect_uri'  => $redirectUri,
                            'code'          => $authCode,
                        ]
                    );
                }

                Log::info('Instagram token exchange', [
                    'status' => $tokenResp->status(),
                    'body'   => $tokenResp->json(),
                ]);

                if ($tokenResp->successful() && isset($tokenResp->json()['access_token'])) {
                    $userAccessToken = $tokenResp->json()['access_token'];
                } else {
                    $errMsg = $tokenResp->json()['error_message'] ?? $tokenResp->json()['error'] ?? 'Gagal menukar kode Instagram.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $errMsg], 400);
                    }
                    return redirect()->route('instagram')->with('gagal', $errMsg);
                }
            }

            Log::info("Instagram Callback started", [
                'has_token' => !empty($userAccessToken),
                'method' => $request->method()
            ]);

            if (!$userAccessToken) {
                // Check for error
                if ($request->has('error')) {
                    return redirect()->route('instagram')
                        ->with('gagal', 'Login dibatalkan.');
                }
                return response()->json(['success' => false, 'message' => 'Token tidak diterima'], 400);
            }

            // Try Instagram Graph API first (for Instagram Login tokens)
            $igResponse = Http::get('https://graph.instagram.com/me', [
                'access_token' => $userAccessToken,
                'fields' => 'id,username',
            ]);

            Log::info("IG Graph API /me", [
                'success' => $igResponse->successful(),
                'data' => $igResponse->json()
            ]);

            if ($igResponse->successful() && !empty($igResponse->json()['id'])) {
                // Instagram token - direct flow
                $igData = $igResponse->json();

                // Get more profile details
                $profileResponse = Http::get("https://graph.instagram.com/{$igData['id']}", [
                    'access_token' => $userAccessToken,
                    'fields' => 'id,username,name,profile_picture_url,followers_count,follows_count,media_count',
                ]);
                $profile = $profileResponse->successful() ? $profileResponse->json() : $igData;

                // Get long-lived token
                $appSecret = platform_currency()->fb_app_secret;
                $longResponse = Http::get('https://graph.instagram.com/access_token', [
                    'grant_type' => 'ig_exchange_token',
                    'client_secret' => $appSecret,
                    'access_token' => $userAccessToken,
                ]);
                $longToken = $userAccessToken;
                if ($longResponse->successful()) {
                    $longToken = $longResponse->json()['access_token'] ?? $userAccessToken;
                }

                $accountData = [
                    'instagram_id' => $profile['id'] ?? $igData['id'],
                    'username' => $profile['username'] ?? $igData['username'] ?? null,
                    'name' => $profile['name'] ?? $profile['username'] ?? null,
                    'profile_picture_url' => $profile['profile_picture_url'] ?? null,
                    'biography' => null,
                    'website' => null,
                    'followers_count' => $profile['followers_count'] ?? 0,
                    'follows_count' => $profile['follows_count'] ?? 0,
                    'media_count' => $profile['media_count'] ?? 0,
                    'page_id' => null,
                    'page_name' => null,
                    'access_token' => $longToken,
                ];

                $this->instagramService->createData($accountData);

                Log::info("Instagram connected via IG Graph API", ['username' => $accountData['username']]);

                $msg = 'Berhasil menghubungkan @' . ($accountData['username'] ?? 'unknown');
                if ($request->isMethod('GET')) {
                    return redirect()->route('instagram')->with('berhasil', $msg);
                }
                return response()->json(['success' => true, 'message' => $msg]);
            }

            // Fallback: try Facebook Graph API (for FB Login tokens)
            Log::info("IG Graph API failed, trying Facebook Graph API...");

            $pages = $this->getUserPages($userAccessToken);

            if (!empty($pages)) {
                $instagramAccounts = $this->getInstagramAccountsFromPages($pages, $userAccessToken);

                if (!empty($instagramAccounts)) {
                    $connectedCount = 0;
                    foreach ($instagramAccounts as $igAcct) {
                        $this->instagramService->createData($igAcct);
                        $connectedCount++;

                        // Auto-subscribe webhook
                        try {
                            $pageToken = $igAcct['page_access_token'] ?? $igAcct['access_token'];
                            $pageId = $igAcct['page_id'] ?? null;
                            if ($pageId) {
                                Http::post("https://graph.facebook.com/{$this->apiVersion}/{$pageId}/subscribed_apps", [
                                    'access_token' => $pageToken,
                                    'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins'
                                ]);
                            }
                        } catch (\Exception $ex) {
                            Log::warning("IG webhook failed", ['error' => $ex->getMessage()]);
                        }
                    }

                    $msg = "Berhasil menghubungkan {$connectedCount} akun Instagram";
                    if ($request->isMethod('GET')) {
                        return redirect()->route('instagram')->with('berhasil', $msg);
                    }
                    return response()->json(['success' => true, 'message' => $msg]);
                }
            }

            $errMsg = 'Tidak ditemukan akun Instagram Professional. Pastikan akun sudah di-switch ke Professional Account.';
            if ($request->isMethod('GET')) {
                return redirect()->route('instagram')->with('gagal', $errMsg);
            }
            return response()->json(['success' => false, 'message' => $errMsg], 400);

        } catch (\Exception $e) {
            Log::error("Instagram Callback Error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function handleViaFacebookPage($userAccessToken)
    {
        // Get user's pages
        $pages = $this->getUserPages($userAccessToken);

        if (empty($pages)) {
            return response()->json([
                'success' => false,
                'message' => 'No Facebook pages found. Pastikan akun Instagram Anda terhubung ke Facebook Page, atau gunakan metode "Login via Instagram".'
            ], 400);
        }

        // Get Instagram accounts from pages
        $instagramAccounts = $this->getInstagramAccountsFromPages($pages, $userAccessToken);

        if (empty($instagramAccounts)) {
            return response()->json([
                'success' => false,
                'message' => 'No Instagram Business accounts found on your Facebook Pages. Pastikan akun Instagram sudah terhubung ke Facebook Page sebagai Professional Account, atau gunakan metode "Login via Instagram".'
            ], 400);
        }

        // Store Instagram accounts
        $connectedCount = 0;
        foreach ($instagramAccounts as $igData) {
            $this->instagramService->createData($igData);
            $connectedCount++;

            // Auto-subscribe page to webhook
            try {
                $pageToken = $igData['page_access_token'] ?? $igData['access_token'];
                $pageId = $igData['page_id'] ?? null;
                if ($pageId) {
                    Http::post("{$this->graphApiUrl}/{$this->apiVersion}/{$pageId}/subscribed_apps", [
                        'access_token' => $pageToken,
                        'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins'
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning("IG Webhook subscribe failed", ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully connected {$connectedCount} Instagram account(s)"
        ]);
    }

    /**
     * Try to subscribe webhook for Instagram messaging
     */
    private function trySubscribeWebhook($userAccessToken)
    {
        try {
            // Get pages and subscribe
            $pages = $this->getUserPages($userAccessToken);
            foreach ($pages as $page) {
                if (isset($page['instagram_business_account'])) {
                    Http::post("{$this->graphApiUrl}/{$this->apiVersion}/{$page['id']}/subscribed_apps", [
                        'access_token' => $page['access_token'],
                        'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins'
                    ]);
                    Log::info("IG webhook subscribed", ['page_id' => $page['id']]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("IG webhook subscribe failed", ['error' => $e->getMessage()]);
        }
    }

    private function exchangeCodeForToken($code)
    {
        $appId = platform_currency()->fb_app_id;
        $appSecret = platform_currency()->fb_app_secret;

        $response = Http::get("{$this->graphApiUrl}/oauth/access_token", [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'code' => $code,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange code for token: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get user's Facebook Pages
     */
    private function getUserPages($accessToken)
    {
        $response = Http::get("{$this->graphApiUrl}/{$this->apiVersion}/me/accounts", [
            'access_token' => $accessToken,
            'fields' => 'id,name,access_token,instagram_business_account{id,username,name,profile_picture_url,biography,website,followers_count,follows_count,media_count}',
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Extract Instagram accounts from Facebook Pages
     */
    private function getInstagramAccountsFromPages($pages, $userAccessToken)
    {
        $instagramAccounts = [];

        foreach ($pages as $page) {
            if (!isset($page['instagram_business_account'])) {
                continue;
            }

            $igAccount = $page['instagram_business_account'];
            $pageAccessToken = $page['access_token'];

            // Exchange for long-lived token
            $longLivedToken = $this->getLongLivedToken($pageAccessToken);

            $instagramAccounts[] = [
                'instagram_id' => $igAccount['id'],
                'username' => $igAccount['username'] ?? null,
                'name' => $igAccount['name'] ?? null,
                'profile_picture_url' => $igAccount['profile_picture_url'] ?? null,
                'biography' => $igAccount['biography'] ?? null,
                'website' => $igAccount['website'] ?? null,
                'followers_count' => $igAccount['followers_count'] ?? 0,
                'follows_count' => $igAccount['follows_count'] ?? 0,
                'media_count' => $igAccount['media_count'] ?? 0,
                'page_id' => $page['id'],
                'page_name' => $page['name'],
                'access_token' => $longLivedToken,
            ];
        }

        return $instagramAccounts;
    }

    /**
     * Exchange short-lived token for long-lived token
     */
    private function getLongLivedToken($shortLivedToken)
    {
        $appId = platform_currency()->fb_app_id;
        $appSecret = platform_currency()->fb_app_secret;

        $response = Http::get("{$this->graphApiUrl}/{$this->apiVersion}/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if (!$response->successful()) {

            return $shortLivedToken;
        }

        $data = $response->json();
        return $data['access_token'] ?? $shortLivedToken;
    }

    /**
     * Store Instagram account to database
     */
    private function storeInstagramAccount($igData, $accessToken)
    {
        $expiresAt = now()->addDays(60);
        $this->instagramService->createData($igData);
    }

    /**
     * Delete Instagram account
     */
    public function destroy(InstagramAccount $instagram)
    {
        $this->unsubscribeWebhook($instagram);

        $instagram->delete();
        return redirect()->route('instagram')
            ->with('success', 'Instagram account disconnected successfully');
    }

    private function unsubscribeWebhook(InstagramAccount $instagram)
    {
        // Get app access token
        $appId = platform_currency()->fb_app_id;
        $appSecret = platform_currency()->fb_app_secret;
        $appAccessToken = "{$appId}|{$appSecret}";

        // Unsubscribe from page subscriptions
        $response = Http::delete("https://graph.facebook.com/v22.0/{$instagram->page_id}/subscribed_apps", [
            'access_token' => $instagram->access_token,
        ]);

        if ($response->successful()) {
          
        } else {
            Log::warning('Failed to unsubscribe webhook', [
                'instagram_id' => $instagram->instagram_id,
                'response' => $response->body(),
            ]);
        }

        return $response->successful();
    }


    /**
     * Refresh account data
     */
    public function syncAccount(Request $request)
    {
        $instagramId = $request->instagram_id;

        $account = InstagramAccount::where('instagram_id', $instagramId)
            ->firstOrFail();

        try {
            // Refresh account data from Instagram API
            $response = Http::get("{$this->graphApiUrl}/{$this->apiVersion}/{$instagramId}", [
                'access_token' => $account->access_token,
                'fields' => 'id,username,name,profile_picture_url,biography,website,followers_count,follows_count,media_count',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch Instagram data');
            }

            $data = $response->json();

            $this->instagramService->updateData($account, $data);

            return response()->json([
                'status' => true,
                'message' => 'Account synced successfully',
                'data' => $account,
            ]);
        } catch (\Exception $e) {


            return response()->json([
                'status' => false,
                'message' => 'Failed to sync account: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, InstagramAccount $instagram)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        return view('instagram.update', ['page'   => 'Edit Akun Instagram', 'breadcumb' => true], compact('fineTunnels', 'instagram', 'users'));
    }

    public function edit(Request $request, InstagramAccount $instagram)
    {
        $this->validate($request, [ 
            'agent'         => 'required|array',
            'method'        => 'required|in:chatbot,ai,all,none',
            'tunnel'        => 'required_if:method,all,ai',
            'daily_limit'   => 'required|in:yes,no',
            'limit'         => 'required_if:daily_limit,yes',
            'certain_day'   => 'required|in:yes,no',
            'days'          => 'required_if:certain_day,yes|array',
            'certain_time'  => 'required|in:yes,no',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i',
        ]);


        try {
           
            $this->instagramService->editData($request, $instagram); 

            return redirect()->route('instagram')->with(['flash' => __('general.success_update')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }
}
