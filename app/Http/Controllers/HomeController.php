<?php

namespace App\Http\Controllers;

use App\Models\Blash\BlashDetail;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\Cms\Page;
use App\Models\InternalSetting;
use App\Models\LiveChat;
use App\Models\Master\Category;
use App\Models\Master\Label;
use App\Models\Setting;
use App\Models\Store\Store;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Models\TelegramKey;
use App\Models\Meta\InstagramAccount;
use App\Models\Meta\MessengerAccount;
use App\Observers\Blash\LogObserver;
use App\Observers\Saas\InternalSettingObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;

class HomeController extends Controller
{

    protected $webSetting;
    protected $logsObserver;
    public function __construct(InternalSettingObserver $internalSettingObserver, LogObserver $logObserver)
    {
        $this->webSetting       = $internalSettingObserver->webSetting();
        $this->logsObserver     = $logObserver;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (check_user()) {

            if (my_user()->role == 'admin') {
                $settings = Setting::withoutGlobalScopes()->where('merchant_id', null)->first(['id']);
                session()->put('businessid', $settings->id);
                return redirect()->route('admin.index');
            }

            if (my_user()->role == 'user') {
                return redirect()->route('index');
            }
        }

        return redirect()->route('login');
    }

    public function redirect()
    {
        $setting = InternalSetting::first(['frontend']);
        if ($setting) {
            if ($setting->frontend == 'yes') {

                $app    = Page::where('page', 'home')->first();
                return view(
                    'web.' . $this->webSetting->web_template . '.home',
                    [
                        'page'          => $app->name,
                        'name'          => $this->webSetting->app_name
                    ],
                    compact('app')
                );

                return redirect()->route('web.home');
            }
        }

        return redirect()->route('login');
    }


    public function home(Request $request)
    {
        // FIX: Cache semua query dashboard 5 menit (300 detik)
        // Data tidak harus real-time detik per detik untuk dashboard summary
        $businessId = my_business();
        $merchantId = my_user()->merchant_id;
        $monthYear  = date('Y-m');

        $summary = Cache::remember("home_summary_{$merchantId}_{$businessId}_{$monthYear}", 900, function () use ($businessId) {
            return [
                'unofficial'  => WhatsappDevice::count(),
                'official'    => WhatsappKeyAccount::count(),
                'livechats'   => LiveChat::count(),
                'telegram'    => TelegramKey::count(),
                'instagram'   => InstagramAccount::count(),
                'messenger'   => MessengerAccount::count(),
                'finetunnels' => FineTunnel::count(),
                'stores'      => Store::count(),
                'categories'  => Category::count(),
                'user'        => User::count(),
                'blast_w'     => BlashDetail::whereHas('parent', function ($q) use ($businessId) {
                    return $q->where('use', 'whatsapp')->where('business_id', $businessId);
                })->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'blast_e'     => BlashDetail::whereHas('parent', function ($q) use ($businessId) {
                    return $q->where('use', 'email')->where('business_id', $businessId);
                })->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'scraping'    => Store::where('scrapping_id', '!=', null)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'sending'     => BlashDetail::whereHas('parent', function ($q) use ($businessId) {
                    return $q->where('business_id', $businessId);
                })->where('reports', null)->where('created_at', '>=', now()->subDays(30))->count(),
                'not_sending' => BlashDetail::whereHas('parent', function ($q) use ($businessId) {
                    return $q->where('business_id', $businessId);
                })->where('reports', '!=', null)->where('created_at', '>=', now()->subDays(30))->count(),
            ];
        });

        $interactions = Cache::remember("home_interactions_{$merchantId}_{$monthYear}", 900, function () {
            // OPTIMIZED: 4 queries → 1 GROUP BY query (saves ~1.8s)
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd   = now()->endOfMonth()->toDateString();

            $counts = HistoryChat::selectRaw("
                    status,
                    COUNT(*) as total,
                    SUM(CASE WHEN handled_by IS NOT NULL THEN 1 ELSE 0 END) as assigned
                ")
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->groupBy('status')
                ->pluck('total', 'status');

            $assignCount = HistoryChat::whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('handled_by')->count();

            return [
                'open'     => $counts['open'] ?? 0,
                'pending'  => $counts['pending'] ?? 0,
                'resolved' => $counts['resolved'] ?? 0,
                'assign'   => $assignCount,
            ];
        });

        $logs = Cache::remember("home_logs_{$merchantId}_{$monthYear}", 60, function () use ($request) {
            return ['whatsapp' => $this->logsObserver->getData($request, 'whatsapp')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at'])];
        });

        // CRM Messages: 5 newest + 5 oldest unanswered
        $crmMessages = Cache::remember("home_crm_{$merchantId}_{$businessId}", 60, function () {
            // 5 newest unread messages (has unread count, sorted newest first)
            $newest = HistoryChat::with(['details' => function($q) {
                    $q->where('from', 'user')->orderBy('created_at', 'desc')->limit(1)->select('history_chat_id', 'message', 'type', 'from');
                }])
                ->where('unread_count', '>', 0)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('last_message_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'avatar_url']);

            // 5 oldest unread messages (has unread count, sorted oldest first)
            $oldest = HistoryChat::with(['details' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(1)->select('history_chat_id', 'message', 'type', 'from');
                }])
                ->where('unread_count', '>', 0)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('last_message_at', 'asc')
                ->limit(5)
                ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'avatar_url', 'created_at']);

            return [
                'newest' => $newest->map(function($chat) {
                    $lastDetail = $chat->details->first();
                    if (!$lastDetail) {
                        $lastDetail = $chat->details()->orderBy('created_at', 'desc')->first(['message', 'type']);
                    }
                    return [
                        'id' => $chat->id,
                        'name' => $chat->name ?? $chat->from_number,
                        'phone' => $chat->from_number,
                        'from' => $chat->from,
                        'status' => $chat->status,
                        'last_message' => $lastDetail->message ?? '-',
                        'last_message_type' => $lastDetail->type ?? 'text',
                        'last_message_at' => $chat->last_message_at,
                        'unread' => $chat->unread_count ?? 0,
                        'avatar' => $chat->avatar_url,
                    ];
                }),
                'oldest' => $oldest->map(function($chat) {
                    $lastDetail = $chat->details()->where('from', 'user')->orderBy('created_at', 'desc')->first(['message', 'type']);
                    if (!$lastDetail) {
                        $lastDetail = $chat->details()->orderBy('created_at', 'desc')->first(['message', 'type']);
                    }
                    $waitTime = $chat->last_message_at 
                        ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans() 
                        : ($chat->created_at ? \Carbon\Carbon::parse($chat->created_at)->diffForHumans() : '-');
                    return [
                        'id' => $chat->id,
                        'name' => $chat->name ?? $chat->from_number,
                        'phone' => $chat->from_number,
                        'from' => $chat->from,
                        'status' => $chat->status,
                        'last_message' => $lastDetail->message ?? '-',
                        'last_message_type' => $lastDetail->type ?? 'text',
                        'last_message_at' => $chat->last_message_at,
                        'wait_time' => $waitTime,
                        'avatar' => $chat->avatar_url,
                    ];
                }),
            ];
        });

        return view('home', ['page' => __('page.dashboard'), 'breadcumb' => false], compact('summary', 'logs', 'interactions', 'crmMessages'));
    }

    /**
     * AJAX endpoint — Pesan Belum Dibalas with optional day filter
     * GET /app/dashboard/unreplied?days=7  (0 = semua)
     */
    public function unrepliedChats(\Illuminate\Http\Request $request)
    {
        $days = (int) $request->get('days', 7);

        $chats = \App\Models\HistoryChat::query()
            ->where('unread_count', '>', 0)
            ->whereIn('status', ['open', 'pending'])
            ->when($days > 0, fn($q) => $q->where('last_message_at', '>=', now()->subDays($days)))
            ->orderBy('last_message_at', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'avatar_url', 'created_at'])
            ->map(function ($chat) {
                $lastDetail = \App\Models\HistoryChat::find($chat->id)
                    ?->details()->where('from', 'user')->orderBy('created_at', 'desc')->first(['message', 'type']);
                if (!$lastDetail) {
                    $lastDetail = \App\Models\HistoryChat::find($chat->id)
                        ?->details()->orderBy('created_at', 'desc')->first(['message', 'type']);
                }
                $waitTime = $chat->last_message_at
                    ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans()
                    : ($chat->created_at ? \Carbon\Carbon::parse($chat->created_at)->diffForHumans() : '-');
                return [
                    'id'                => $chat->id,
                    'name'              => $chat->name ?? $chat->from_number,
                    'phone'             => $chat->from_number,
                    'from'              => $chat->from,
                    'status'            => $chat->status,
                    'last_message'      => $lastDetail->message ?? '-',
                    'last_message_type' => $lastDetail->type ?? 'text',
                    'last_message_at'   => $chat->last_message_at,
                    'wait_time'         => $waitTime,
                ];
            });

        return response()->json($chats);
    }

    public function interactionAnalysis()
    {

        $interactions = HistoryChat::selectRaw("
            YEARWEEK(created_at, 1) as yearweek,
            MIN(DATE(created_at)) as start_date,
            COUNT(*) as count
        ")
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->groupBy('yearweek')
            ->orderBy('start_date')
            ->get();

        return response()->json($interactions);
    }

    public function leadByLabel(Request $request)
    {
        $businessId = my_business();
        $cacheKey = "label_leads_{$businessId}";

        $cached = \Cache::get($cacheKey);
        if ($cached) return response()->json($cached);

        $labels = Label::select('id', 'name', 'color')
            ->get()
            ->map(function ($label) {
                $count = \App\Models\ChatBot\HistoryChat::whereJsonContains('label', ['id' => $label->id])->count();
                return [
                    'label' => $label->name,
                    'data'  => $count,
                    'color' => $label->color ?? '#0EA5E9'
                ];
            })
            ->filter(fn($item) => $item['data'] > 0)
            ->values();

        $result = ['labels' => $labels];
        \Cache::put($cacheKey, $result, 300);

        return response()->json($result, 200);
    }

    public function analiss(Request $request)
    {
        $senderData     = array();
        $notSenderData  = array();
        $dateData       = array();

        $blashData = BlashDetail::whereHas('parent', function ($q) {
            return $q->where("merchant_id", my_user()->merchant_id);
        })->selectRaw('LEFT(created_at, 10) as date, 
        SUM(CASE WHEN reports IS NULL THEN 1 ELSE 0 END) AS sending,
        SUM(CASE WHEN reports IS NOT NULL THEN 1 ELSE 0 END) AS not_sending')
            ->where('created_at', ">=", now()->subDays(30))
            ->groupBy('date')
            ->get();

        foreach ($blashData as $blash) {
            $dateData[]             = Carbon::parse($blash->date, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->format('d, M Y');
            $senderData[]           = (int)$blash->sending;
            $notSenderData[]        = (int)$blash->not_sending;
        }

        return response()->json([
            'analisis_blash'    => array(
                'sender'            => $senderData,
                'not_sender'        => $notSenderData,
                'date'              => $dateData,
            ),
        ]);
    }

    public function chats(Request $request)
    {
        return view('chats', ['page'     => 'Whatsmail Live Chat']);
    }

    public function logs(Request $request)
    {
        $logs   = $this->logsObserver->getData($request, $request->type)->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']);
        return response()->json($logs);
    }

    public function policy()
    {
        return view('page.policy', ['page'   => 'Kebijakan Privasi']);
    }

    public function term()
    {
        return view('page.term', ['page'   => 'Kebijakan Privasi']);
    }

    public function handleFacebookCallback(Request $request)
    {

        $userDenyRequest    = $request->input('error');
        $provider           = 'facebook';

        // check the request is deny then redirect user on login page
        if (isset($userDenyRequest) and $userDenyRequest === 'access_denied') {
            return redirect()->route('login');
        }

        try {

            config([
                'services.facebook.redirect'        => route('login.facebook.callback'),
                'services.facebook.client_id'       => platform_currency()->fb_app_id,
                'services.facebook.client_secret'   => platform_currency()->fb_app_secret,
            ]);

            $fbUser = Socialite::driver('facebook')->user();

            if ($fbUser) {
                return response([
                    'status'    => true,
                    'message'   => 'Berhasil'
                ], 200);
            }

            return response([
                'status'    => false,
                'message'   => 'Pengguna tidak ditemukan'
            ], 401);
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Facebook');
        }
    }

    public function redirectToFacebook()
    {
        config([
            'services.facebook.redirect'        => route('login.facebook.callback'),
            'services.facebook.client_id'       => platform_currency()->fb_app_id,
            'services.facebook.client_secret'   => platform_currency()->fb_app_secret,
        ]);

        return Socialite::driver('facebook')->redirect();
    }

    public function broadcastStatus()
    {
        $businessId = my_business();
        $merchantId = my_user()->merchant_id;
        $cacheKey   = "broadcast_status_{$merchantId}_{$businessId}";

        $data = Cache::remember($cacheKey, 300, function () use ($businessId) {
            $broadcasts = \DB::select("
                SELECT
                    bw.id,
                    bw.name,
                    bw.use,
                    bw.created_at,
                    COUNT(bd.id) as total,
                    SUM(CASE WHEN bd.sending_status = 'yes' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN bd.sending_status = 'no'  THEN 1 ELSE 0 END) as failed
                FROM blash_whatsapps bw
                LEFT JOIN blash_details bd
                    ON bd.blash_whatsapp_id = bw.id AND bd.type = 'whatsapp'
                WHERE bw.business_id = ?
                GROUP BY bw.id, bw.name, bw.use, bw.created_at
                ORDER BY bw.created_at DESC
                LIMIT 5
            ", [$businessId]);

            $result = [];
            foreach ($broadcasts as $b) {
                $total  = (int) $b->total;
                $sent   = (int) $b->sent;
                $failed = (int) $b->failed;
                $rate   = $total > 0 ? round($sent / $total * 100, 1) : 0;

                $devices = \DB::select("
                    SELECT
                        bd.device_id,
                        COALESCE(wd.name, wka.phone, 'Unknown')                        AS device_name,
                        COALESCE(wd.phone, wka.phone, '-')                              AS device_phone,
                        CASE WHEN wka.id IS NOT NULL THEN 'WABA' ELSE 'Personal' END   AS device_type,
                        COUNT(bd.id)                                                      AS total,
                        SUM(CASE WHEN bd.sending_status = 'yes' THEN 1 ELSE 0 END)     AS sent,
                        SUM(CASE WHEN bd.sending_status = 'no'  THEN 1 ELSE 0 END)     AS failed
                    FROM blash_details bd
                    LEFT JOIN whatsapp_devices      wd  ON wd.id  = bd.device_id
                    LEFT JOIN whatsapp_key_accounts wka ON wka.id = bd.device_id
                    WHERE bd.blash_whatsapp_id = ?
                      AND bd.type = 'whatsapp'
                    GROUP BY bd.device_id, device_name, device_phone, device_type
                    ORDER BY sent DESC
                ", [$b->id]);

                $deviceData = [];
                foreach ($devices as $d) {
                    $dTotal = (int) $d->total;
                    $dSent  = (int) $d->sent;
                    $deviceData[] = [
                        'name'        => $d->device_name,
                        'phone'       => $d->device_phone,
                        'device_type' => $d->device_type,
                        'total'       => $dTotal,
                        'sent'        => $dSent,
                        'failed'      => (int) $d->failed,
                        'rate'        => $dTotal > 0 ? round($dSent / $dTotal * 100, 1) : 0,
                    ];
                }

                $result[] = [
                    'id'         => $b->id,
                    'name'       => $b->name,
                    'use'        => $b->use,
                    'total'      => $total,
                    'sent'       => $sent,
                    'failed'     => $failed,
                    'rate'       => $rate,
                    'created_at' => $b->created_at,
                    'devices'    => $deviceData,
                ];
            }
            return $result;
        });

        return response()->json($data);
    }


    /**
     * Pesan Masuk per hari (customer messages only, not broadcasts)
     */
    public function pesanMasuk(Request $request)
    {
        $businessId = my_business();
        
        $days = (int) ($request->days ?? 7);
        if ($days > 90) $days = 90;

        $cacheKey = "pesan_masuk_{$businessId}_{$days}";
        $cached = \Cache::get($cacheKey);
        if ($cached) return response()->json($cached);
        
        $startDate = now()->subDays($days)->startOfDay()->toDateTimeString();
        $endDate = now()->endOfDay()->toDateTimeString();
        
        // Count incoming messages from customers per day
        $data = \DB::select("
            SELECT DATE(hcd.created_at) as date, COUNT(*) as total
            FROM history_chat_details hcd
            INNER JOIN history_chats hc ON hc.id = hcd.history_chat_id
            WHERE hcd.`from` = 'user'
            AND hc.business_id = ?
            AND hcd.created_at >= ?
            AND hcd.created_at <= ?
            GROUP BY DATE(hcd.created_at)
            ORDER BY date ASC
        ", [$businessId, $startDate, $endDate]);
        
        $dates = [];
        $totals = [];
        $grandTotal = 0;
        foreach ($data as $row) {
            $dates[] = Carbon::parse($row->date)->format('d M');
            $totals[] = (int) $row->total;
            $grandTotal += (int) $row->total;
        }
        
        $result = [
            'dates' => $dates,
            'totals' => $totals,
            'grand_total' => $grandTotal,
        ];
        \Cache::put($cacheKey, $result, 300);
        return response()->json($result);
    }
    
    /**
     * Broadcast summary per hari (grouped by broadcast campaign date)
     */
    public function broadcastSummary(Request $request)
    {
        $businessId = my_business();
        
        $days = (int) ($request->days ?? 7);
        if ($days > 90) $days = 90;

        $cacheKey = "broadcast_summary_{$businessId}_{$days}";
        $cached = \Cache::get($cacheKey);
        if ($cached) return response()->json($cached);
        
        $startDate = now()->subDays($days)->startOfDay()->toDateTimeString();
        $endDate = now()->endOfDay()->toDateTimeString();
        
        // Group by the broadcast campaign date instead of individual detail
        $data = \DB::select("
            SELECT DATE(bw.created_at) as date,
                   SUM(CASE WHEN bd.sending_status = 'yes' THEN 1 ELSE 0 END) as sent,
                   SUM(CASE WHEN bd.sending_status != 'yes' THEN 1 ELSE 0 END) as failed,
                   COUNT(bd.id) as total
            FROM blash_whatsapps bw
            LEFT JOIN blash_details bd ON bd.blash_whatsapp_id = bw.id
            WHERE bw.business_id = ?
            AND bw.created_at >= ?
            AND bw.created_at <= ?
            GROUP BY DATE(bw.created_at)
            ORDER BY date ASC
        ", [$businessId, $startDate, $endDate]);
        
        $dates = [];
        $sent = [];
        $failed = [];
        $grandSent = 0;
        $grandFailed = 0;
        foreach ($data as $row) {
            $dates[] = Carbon::parse($row->date)->format('d M');
            $sent[] = (int) $row->sent;
            $failed[] = (int) $row->failed;
            $grandSent += (int) $row->sent;
            $grandFailed += (int) $row->failed;
        }
        
        $result = [
            'dates' => $dates,
            'sent' => $sent,
            'failed' => $failed,
            'grand_sent' => $grandSent,
            'grand_failed' => $grandFailed,
        ];
        \Cache::put($cacheKey, $result, 300); // 5 min cache
        return response()->json($result);
    }

}