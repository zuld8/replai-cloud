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
            // FIX: 4 query whereHas lambat (scan jutaan blash_details) → 3 query efisien
            // Step 1: Ambil id broadcast bisnis ini dari blash_whatsapps (ratusan baris, bukan jutaan)
            $monthStart = now()->startOfMonth();
            $monthEnd   = now()->endOfMonth();
            $since30    = now()->subDays(30);

            $bw       = \DB::table('blash_whatsapps')->where('business_id', $businessId)->get(['id', 'use']);
            $waIds    = $bw->where('use', 'whatsapp')->pluck('id')->all();
            $emailIds = $bw->where('use', 'email')->pluck('id')->all();
            $allIds   = $bw->pluck('id')->all();

            // Step 2: COUNT blash_details bulan ini — whereIn pakai index blash_whatsapp_id (cepat)
            $blastW = empty($waIds)    ? 0 : BlashDetail::whereIn('blash_whatsapp_id', $waIds)
                          ->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $blastE = empty($emailIds) ? 0 : BlashDetail::whereIn('blash_whatsapp_id', $emailIds)
                          ->whereBetween('created_at', [$monthStart, $monthEnd])->count();

            // Step 3: sending + not_sending dalam 1 query SUM (bukan 2 query terpisah)
            $snd = empty($allIds) ? null : \DB::table('blash_details')
                ->whereIn('blash_whatsapp_id', $allIds)
                ->where('created_at', '>=', $since30)
                ->selectRaw('SUM(reports IS NULL) AS sending, SUM(reports IS NOT NULL) AS not_sending')
                ->first();

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
                'blast_w'     => $blastW,
                'blast_e'     => $blastE,
                'scraping'    => Store::whereNotNull('scrapping_id')->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'sending'     => (int) ($snd->sending     ?? 0),
                'not_sending' => (int) ($snd->not_sending ?? 0),
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

        $logs = Cache::remember("home_logs_{$merchantId}_{$monthYear}", 300, function () use ($request) {  // 5 min — log ringan
            return ['whatsapp' => $this->logsObserver->getData($request, 'whatsapp')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at'])];
        });

        // CRM Messages: 5 newest + 5 oldest unanswered
        $crmMessages = Cache::remember("home_crm_{$merchantId}_{$businessId}", 600, function () { // 10 min — preview chat, gak harus real-time
            // 5 newest unread messages (has unread count, sorted newest first)
            $newest = HistoryChat::with(['last_message'])
                ->where('unread_count', '>', 0)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('last_message_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'avatar_url']);

            // 5 oldest unread messages (has unread count, sorted oldest first)
            $oldest = HistoryChat::with(['last_message'])
                ->where('unread_count', '>', 0)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('last_message_at', 'asc')
                ->limit(5)
                ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'avatar_url', 'created_at']);

            return [
                'newest' => $newest->map(function($chat) {
                    // FIX: pakai last_message (hasOne desc) — tidak kena bug limit/order ASC
                    $lastDetail = $chat->last_message;
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
                    // FIX: pakai last_message (hasOne desc)
                    $lastDetail = $chat->last_message;
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
        // Cache per merchant per bulan — berat, data mingguan jarang berubah
        $merchantId = my_user()->merchant_id;
        $monthYear  = now()->format('Y-m');
        $cacheKey   = "home_interaction_analysis_{$merchantId}_{$monthYear}";

        $interactions = \Cache::remember($cacheKey, 900, function () {
            return HistoryChat::selectRaw("
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
        });

        return response()->json($interactions);
    }

    public function leadByLabel(Request $request)
    {
        $businessId = my_business();
        $cacheKey   = "label_leads_{$businessId}";

        $cached = \Cache::get($cacheKey);
        if ($cached) return response()->json($cached);

        // 1 query instead of N (one per label) — pull all label JSON, count in PHP
        $allLabelJson = \App\Models\ChatBot\HistoryChat::where('business_id', $businessId)
            ->whereNotNull('label')
            ->where('label', '!=', '[]')
            ->where('label', '!=', 'null')
            ->pluck('label');

        $counts = [];
        foreach ($allLabelJson as $raw) {
            $items = is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
            foreach ($items as $item) {
                $id = $item['id'] ?? null;
                if ($id) $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }

        $labels = Label::select('id', 'name', 'color')
            ->get()
            ->map(fn($label) => [
                'label' => $label->name,
                'data'  => $counts[$label->id] ?? 0,
                'color' => $label->color ?? '#0EA5E9',
            ])
            ->filter(fn($item) => $item['data'] > 0)
            ->values();

        $result = ['labels' => $labels];
        \Cache::put($cacheKey, $result, 1800); // 30 min — label data rarely changes

        return response()->json($result, 200);
    }

    public function analiss(Request $request)
    {
        // Cache per merchant 15 menit — analisis blast 30 hari, berat di blash_details
        $merchantId = my_user()->merchant_id;
        $cacheKey   = "home_analiss_{$merchantId}";

        $result = \Cache::remember($cacheKey, 900, function () {
            $senderData    = [];
            $notSenderData = [];
            $dateData      = [];

            $blashData = BlashDetail::whereHas('parent', function ($q) {
                return $q->where("merchant_id", my_user()->merchant_id);
            })->selectRaw('LEFT(created_at, 10) as date,
            SUM(CASE WHEN reports IS NULL THEN 1 ELSE 0 END) AS sending,
            SUM(CASE WHEN reports IS NOT NULL THEN 1 ELSE 0 END) AS not_sending')
                ->where('created_at', ">=", now()->subDays(30))
                ->groupBy('date')
                ->get();

            foreach ($blashData as $blash) {
                $dateData[]      = Carbon::parse($blash->date, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->format('d, M Y');
                $senderData[]    = (int)$blash->sending;
                $notSenderData[] = (int)$blash->not_sending;
            }

            return [
                'analisis_blash' => [
                    'sender'     => $senderData,
                    'not_sender' => $notSenderData,
                    'date'       => $dateData,
                ],
            ];
        });

        return response()->json($result);
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

        // TTL naik 600s agar warming tiap 10 menit selalu nutupin
        $data = Cache::remember($cacheKey, 600, function () use ($businessId) {
            // FIX: Ambil 5 broadcast DULU (ringan, tidak nyentuh blash_details)
            // Query lama: JOIN + GROUP BY seluruh riwayat bisnis → baru LIMIT 5 = 55 detik!
            // Query baru: 2 query kecil → total <1 detik
            $broadcasts = \DB::select("
                SELECT id, name, `use`, created_at
                FROM blash_whatsapps
                WHERE business_id = ?
                ORDER BY created_at DESC
                LIMIT 5
            ", [$businessId]);

            if (empty($broadcasts)) return [];

            $ids          = array_map(fn($b) => $b->id, $broadcasts);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            // Agregasi HANYA untuk 5 broadcast (bukan seluruh riwayat bisnis)
            $totalsRaw = \DB::select("
                SELECT blash_whatsapp_id,
                       COUNT(*)                              AS total,
                       SUM(sending_status = 'yes')           AS sent,
                       SUM(sending_status = 'no')            AS failed
                FROM blash_details
                WHERE blash_whatsapp_id IN ($placeholders) AND type = 'whatsapp'
                GROUP BY blash_whatsapp_id
            ", $ids);
            $totals = collect($totalsRaw)->keyBy('blash_whatsapp_id');

            $result = [];
            foreach ($broadcasts as $b) {
                $t      = $totals->get($b->id);
                $total  = (int) ($t->total  ?? 0);
                $sent   = (int) ($t->sent   ?? 0);
                $failed = (int) ($t->failed ?? 0);
                $rate   = $total > 0 ? round($sent / $total * 100, 1) : 0;

                // Per-device: scoped ke 1 broadcast → ringan
                $devices = \DB::select("
                    SELECT
                        bd.device_id,
                        COALESCE(wd.name, wka.phone, 'Unknown')                        AS device_name,
                        COALESCE(wd.phone, wka.phone, '-')                             AS device_phone,
                        CASE WHEN wka.id IS NOT NULL THEN 'WABA' ELSE 'Personal' END  AS device_type,
                        COUNT(bd.id)                                                   AS total,
                        SUM(CASE WHEN bd.sending_status = 'yes' THEN 1 ELSE 0 END)   AS sent,
                        SUM(CASE WHEN bd.sending_status = 'no'  THEN 1 ELSE 0 END)   AS failed
                    FROM blash_details bd
                    LEFT JOIN whatsapp_devices      wd  ON wd.id  = bd.device_id
                    LEFT JOIN whatsapp_key_accounts wka ON wka.id = bd.device_id
                    WHERE bd.blash_whatsapp_id = ? AND bd.type = 'whatsapp'
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


    public function leadReport(Request $request)
    {
        $businessId = my_business();
        $cacheKey   = "lead_report_{$businessId}";

        $cached = \Cache::get($cacheKey);
        if ($cached) return response()->json($cached);

        // GROUP BY ad headline (JSON extract) — 1 query for all ads
        $rows = \App\Models\ChatBot\HistoryChat::where('business_id', $businessId)
            ->whereNotNull('lead_source')
            ->selectRaw("
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(lead_source_detail, '$.headline')), lead_source) as headline,
                lead_source,
                COUNT(*) as total
            ")
            ->groupByRaw("JSON_EXTRACT(lead_source_detail, '$.headline'), lead_source")
            ->orderByDesc('total')
            ->get();

        $organic = \App\Models\ChatBot\HistoryChat::where('business_id', $businessId)
            ->whereNull('lead_source')
            ->count();

        $result = ['rows' => $rows, 'organic' => $organic];
        \Cache::put($cacheKey, $result, 1800); // 30 min cache

        return response()->json($result, 200);
    }

}