<?php





namespace App\Http\Controllers\Facebook;





use App\Http\Controllers\Controller;


use App\Models\Meta\MessengerAccount;


use App\Observers\ChatBot\FineTunnelObserver;


use App\Observers\UserObserver;


use App\Services\Platform\MesaangerService;


use Illuminate\Http\Request;


use Illuminate\Support\Facades\Http;


use Illuminate\Support\Facades\Log;





class MessangerController extends Controller


{


    protected $messengerService;


    protected $usersObserver;


    protected $fineTunnelObserver;





    public function __construct(MesaangerService $messengerService, UserObserver $usersObserver, FineTunnelObserver $fineTunnelObserver)


    {


        $this->messengerService = $messengerService;


        $this->usersObserver        = $usersObserver;


        $this->fineTunnelObserver   = $fineTunnelObserver;


    }





    /**


     * Display messenger accounts


     */


    public function index(Request $request)


    {


        $accounts = $this->messengerService->getData($request)->get();





        return view('messanger.index', ['page'   => 'Akun Messenger'], compact('accounts'));


    }





    /**


     * Handle OAuth callback


     */
    public function handleCallback(Request $request)
    {
        try {
            // Accept accessToken from AJAX POST
            $userAccessToken = $request->input('access_token');
            $authCode        = $request->input('code');

            // Exchange authorization code for access token (Meta API v22+)
            if ($authCode && !$userAccessToken) {
                $platform  = platform_currency();
                $tokenResp = \Illuminate\Support\Facades\Http::get('https://graph.facebook.com/oauth/access_token', [
                    'client_id'     => $platform->fb_app_id,
                    'client_secret' => $platform->fb_app_secret,
                    'code'          => $authCode,
                    'redirect_uri'  => '',
                ]);
                if ($tokenResp->successful() && isset($tokenResp->json()['access_token'])) {
                    $userAccessToken = $tokenResp->json()['access_token'];
                    Log::info('Messenger auth code exchanged for token OK');
                } else {
                    Log::error('Messenger code exchange failed', $tokenResp->json());
                    return response()->json(['success' => false, 'message' => 'Gagal menukar kode otorisasi. Coba lagi.'], 400);
                }
            }
            Log::info("Messenger Callback started", [
                'has_token' => !empty($userAccessToken),
                'method' => $request->method()
            ]);

            if (!$userAccessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token not received'
                ], 400);
            }

            // ── Exchange short-lived → long-lived token (page tokens won't expire) ─
            try {
                $platform = platform_currency();
                if ($platform && !empty($platform->fb_app_id) && !empty($platform->fb_app_secret)) {
                    $llResp = Http::get('https://graph.facebook.com/oauth/access_token', [
                        'grant_type'        => 'fb_exchange_token',
                        'client_id'         => $platform->fb_app_id,
                        'client_secret'     => $platform->fb_app_secret,
                        'fb_exchange_token' => $userAccessToken,
                    ]);
                    if ($llResp->successful() && isset($llResp->json()['access_token'])) {
                        $userAccessToken = $llResp->json()['access_token'];
                        Log::info('Messenger: long-lived token exchange OK');
                    } else {
                        Log::warning('Messenger: long-lived exchange failed', $llResp->json() ?? []);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Messenger: long-lived token exception: ' . $e->getMessage());
            }

            // Get user's pages using the (now long-lived) access token
            $pagesResponse = Http::get('https://graph.facebook.com/v22.0/me/accounts', [
                'access_token' => $userAccessToken
            ]);

            Log::info("Messenger pages response", [
                'success' => $pagesResponse->successful(),
                'data' => $pagesResponse->json()
            ]);

            if (!$pagesResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get Facebook pages: ' . json_encode($pagesResponse->json())
                ], 400);
            }

            $pages = $pagesResponse->json()['data'] ?? [];

            if (empty($pages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Facebook pages found. Please create a page first.'
                ], 400);
            }

            // Process each page
            $connectedCount = 0;
            foreach ($pages as $page) {
                Log::info("Processing page", [
                    'page_id' => $page['id'],
                    'page_name' => $page['name'] ?? 'unknown'
                ]);
                $pageData = $this->getPageDetails($page['id'], $page['access_token']);

                if ($pageData) {
                    $this->messengerService->createData($pageData);
                    
                    // Auto-subscribe page to webhook
                    try {
                        $subscribeResponse = Http::post("https://graph.facebook.com/v22.0/{$page['id']}/subscribed_apps", [
                            'access_token' => $page['access_token'],
                            'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins,message_deliveries,message_reads,messaging_referrals'
                        ]);
                        Log::info("Webhook auto-subscribe", ['page_id' => $page['id'], 'success' => $subscribeResponse->successful()]);
                    } catch (\Exception $e) {
                        Log::warning("Webhook subscribe failed", ['page_id' => $page['id'], 'error' => $e->getMessage()]);
                    }
                    $connectedCount++;
                }
            }

            Log::info("Messenger connection complete", ['connected' => $connectedCount]);

            return response()->json([
                'success' => true,
                'message' => "Successfully connected {$connectedCount} Facebook Page(s)"
            ]);
        } catch (\Exception $e) {
            Log::error("Messenger Callback Error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

/**
     * Get detailed page information
     */


    private function getPageDetails(string $pageId, string $pageAccessToken): ?array


    {


        try {


            $response = Http::get("https://graph.facebook.com/v22.0/{$pageId}", [


                'fields' => 'name,username,picture,category,about,phone,emails,website,fan_count',


                'access_token' => $pageAccessToken


            ]);





            if ($response->successful()) {


                $data = $response->json();





                return [


                    'page_id' => $pageId,


                    'page_name' => $data['name'] ?? 'Unknown',


                    'page_username' => $data['username'] ?? null,


                    'page_picture_url' => $data['picture']['data']['url'] ?? null,


                    'category' => $data['category'] ?? null,


                    'about' => $data['about'] ?? null,


                    'phone' => $data['phone'] ?? null,


                    'email' => $data['emails'][0] ?? null,


                    'website' => $data['website'] ?? null,


                    'followers_count' => $data['fan_count'] ?? 0,


                    'access_token' => $pageAccessToken,


                ];


            }





            return null;


        } catch (\Exception $e) {


            Log::error('Failed to get page details: ' . $e->getMessage());


            return null;


        }


    }





    /**


     * Sync page data


     */


    public function syncAccount(Request $request)


    {


        $request->validate([


            'page_id' => 'required|string'


        ]);





        $messenger = MessengerAccount::where('page_id', $request->page_id)->first();





        if (!$messenger) {


            return response()->json([


                'status' => false,


                'message' => 'Messenger account not found'


            ], 404);


        }





        $result = $this->messengerService->syncPageData($messenger);





        return response()->json($result);


    }





    /**


     * Show update form


     */


    public function update(Request $request, MessengerAccount $messenger)


    {


        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);


        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);


        return view('messanger.update', ['page'  => 'Edit Pengaturan Messenger'], compact('messenger', 'users', 'fineTunnels'));


    }





    /**


     * Update messenger settings


     */


    public function edit(Request $request, MessengerAccount $messenger)


    {


        $request->validate([


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





        $this->messengerService->editData($request, $messenger);





        return redirect()->route('messenger')


            ->with('success', 'Messenger settings updated successfully');


    }





    /**


     * Disconnect messenger account


     */


    public function destroy(MessengerAccount $messenger)


    {


        try {


            $this->unsubscribeWebhook($messenger);





            $messenger->delete();





            return response()->json([


                'status' => true,


                'message' => 'Messenger account disconnected successfully'


            ]);


        } catch (\Exception $e) {


            Log::error('Failed to disconnect Messenger account', [


                'error' => $e->getMessage(),


                'messenger_id' => $messenger->id


            ]);





            return response()->json([


                'status' => false,


                'message' => 'Failed to disconnect: ' . $e->getMessage()


            ], 500);


        }


    }





    /**


     * Unsubscribe webhook from Facebook Page


     */


    private function unsubscribeWebhook(MessengerAccount $messenger): void


    {


        try {


            $response = Http::delete(


                "https://graph.facebook.com/v22.0/{$messenger->page_id}/subscribed_apps",


                [


                    'access_token' => $messenger->access_token


                ]


            );





            if ($response->successful()) {


            } else {


                Log::warning('Failed to unsubscribe webhook', [


                    'page_id' => $messenger->page_id,


                    'response' => $response->json()


                ]);


            }


        } catch (\Exception $e) {


            Log::error('Error unsubscribing webhook', [


                'page_id' => $messenger->page_id,


                'error' => $e->getMessage()


            ]);


            // Don't throw, continue with deletion


        }


    }


}