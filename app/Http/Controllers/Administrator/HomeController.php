<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashDetail;
use App\Models\Master\Category;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\LiveChat as LiveChatModel;
use App\Models\Merchant\Merchant;
use App\Models\Meta\InstagramAccount;
use App\Models\Meta\MessengerAccount;
use App\Models\Package\PackageTransaction;
use App\Models\Setting;
use App\Models\Store\Scrapping;
use App\Models\Store\Store;
use App\Models\TelegramKey;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Observers\Blash\LogObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $logsObserver;

    public function __construct(LogObserver $logObserver)
    {
        $this->middleware('auth');
        $this->logsObserver = $logObserver;
    }

    public function home(Request $request)
    {
        $monthYear  = date('Y-m');
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $monthEnd   = now()->endOfMonth()->toDateTimeString();
        $range      = in_array((int)$request->input('range'), [7, 30]) ? (int)$request->input('range') : 30;

        // ── SUMMARY: KPI utama (cached 15 mnt) ──
        $summary = Cache::remember("admin_summary_{$monthYear}", 900, function () use ($monthStart, $monthEnd) {
            return [
                'merchants'   => Merchant::count(),
                // FIX BUG 1: withoutGlobalScopes agar gak kena filter business_id=null
                'business'    => Setting::withoutGlobalScopes()->whereNotNull('merchant_id')->count(),
                'packages'    => PackageTransaction::where('status', 'success')->where('type', 'package')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                'topup'       => PackageTransaction::where('status', 'success')->where('type', 'topup')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                'finetunnels' => FineTunnel::withoutGlobalScopes()->count(),
                'users'       => User::withoutGlobalScopes()->count(),
                'devices'     => WhatsappDevice::withoutGlobalScopes()->count(),
                // FIX BUG 2: pisah type whatsapp vs email
                'blast_w'     => BlashDetail::where('type', 'whatsapp')->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'blast_e'     => BlashDetail::where('type', 'email')->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'scraping'    => Store::where('scrapping_id', '!=', null)->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        });

        // ── CHANNEL: jumlah per platform (cached) ──
        $channels = Cache::remember("admin_channels_{$monthYear}", 900, fn () => [
            'waba'      => WhatsappKeyAccount::withoutGlobalScopes()->count(),
            'wa_pers'   => WhatsappDevice::withoutGlobalScopes()->count(),
            'instagram' => InstagramAccount::withoutGlobalScopes()->count(),
            'messenger' => MessengerAccount::withoutGlobalScopes()->count(),
            'telegram'  => TelegramKey::withoutGlobalScopes()->count(),
            'livechat'  => LiveChatModel::withoutGlobalScopes()->count(),
        ]);

        // ── SUBSCRIPTION HEALTH (cached) ──
        $sub = Cache::remember("admin_sub_health_{$monthYear}", 900, function () {
            $totalBiz = Setting::withoutGlobalScopes()->whereNotNull('merchant_id')->count();
            $aktif    = PackageTransaction::where('status', 'success')
                ->where('type', 'package')
                ->where('expire_date', '>=', now())
                ->distinct('business_id')
                ->count('business_id');
            return [
                'total'       => $totalBiz,
                'aktif'       => $aktif,
                'tanpa_paket' => max(0, $totalBiz - $aktif),
                'konversi'    => $totalBiz > 0 ? round($aktif / $totalBiz * 100) : 0,
            ];
        });

        // ── MRR: 12 bulan terakhir (cached) ──
        $mrr = Cache::remember("admin_mrr_12m", 900, fn () =>
            PackageTransaction::where('status', 'success')
                ->where('type', 'package')
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as ym, SUM(final_total) as total")
                ->groupBy('ym')->orderBy('ym')->get()
        );
        $mrrThisMonth = (int) ($mrr->last()->total ?? 0);
        $mrrPrevMonth = (int) ($mrr->count() >= 2 ? $mrr->slice(-2, 1)->first()->total ?? 0 : 0);
        $mrrGrowth    = $mrrPrevMonth > 0 ? round(($mrrThisMonth - $mrrPrevMonth) / $mrrPrevMonth * 100) : 0;

        // ── AI USAGE (cached) ──
        $ai = Cache::remember("admin_ai_usage_{$monthYear}", 900, function () use ($monthStart, $monthEnd) {
            $row = HistoryChatDetail::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('from', 'device')
                ->selectRaw("
                    SUM(CASE WHEN source='bot' THEN 1 ELSE 0 END) as ai_replies,
                    SUM(CASE WHEN source IN ('bot','flow') THEN 1 ELSE 0 END) as automated,
                    COUNT(*) as total_out
                ")->first();
            $total = (int)($row->total_out ?? 0);
            $auto  = (int)($row->automated ?? 0);
            return [
                'ai_replies' => (int)($row->ai_replies ?? 0),
                'automation' => $total > 0 ? round($auto / $total * 100) : 0,
                'training'   => FineTunnel::withoutGlobalScopes()->count(),
            ];
        });

        // AI credit total bulan ini (cached)
        $aiCreditTotal = Cache::remember("admin_credit_ai_total_{$monthYear}", 900, fn () =>
            HistoryChatDetail::whereBetween('created_at', [$monthStart, $monthEnd])->sum('credit_using')
        );

        // ── AI TOP 5 per bisnis (cached) ──
        $aiTopRaw = Cache::remember("admin_ai_top_{$monthYear}", 900, fn () =>
            HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
                ->whereBetween('history_chat_details.created_at', [$monthStart, $monthEnd])
                ->where('history_chat_details.source', 'bot')
                ->selectRaw('history_chats.business_id, COUNT(*) as n')
                ->groupBy('history_chats.business_id')
                ->orderByDesc('n')->limit(5)->get()
        );
        $aiTopBizIds = $aiTopRaw->pluck('business_id')->filter()->values();
        $aiTopBizMap = $aiTopBizIds->isNotEmpty()
            ? Setting::withoutGlobalScopes()->whereIn('id', $aiTopBizIds)->pluck('name', 'id')
            : collect();
        $aiTopTotal  = max(1, $aiTopRaw->sum('n'));
        $aiTop = $aiTopRaw->map(fn ($r) => [
            'name'  => $aiTopBizMap[$r->business_id] ?? 'Bisnis',
            'count' => (int)$r->n,
            'pct'   => round($r->n / $aiTopTotal * 100),
        ]);

        // ── BISNIS AKTIF TERKINI top 10 (cached 5 mnt) ──
        $activeBizRaw = Cache::remember("admin_active_biz", 300, fn () =>
            HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
                ->where('history_chat_details.created_at', '>=', now()->subDays(7))
                ->selectRaw('history_chats.business_id,
                             MAX(history_chat_details.created_at) as last_activity,
                             COUNT(*) as chat_7d')
                ->groupBy('history_chats.business_id')
                ->orderByDesc('last_activity')->limit(10)->get()
        );
        $activeBizIds = $activeBizRaw->pluck('business_id')->filter()->values();
        $activeBizMap = $activeBizIds->isNotEmpty()
            ? Setting::withoutGlobalScopes()->whereIn('id', $activeBizIds)->get(['id', 'name'])->keyBy('id')
            : collect();
        $activeBiz = $activeBizRaw->map(fn ($r) => [
            'biz'     => $activeBizMap[$r->business_id] ?? null,
            'last'    => $r->last_activity,
            'chat_7d' => (int)$r->chat_7d,
        ]);

        // ── SCRAPING BY METHOD (cached) ──
        $scrap = Cache::remember("admin_scrap_{$monthYear}", 900, fn () =>
            Scrapping::withoutGlobalScopes()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->selectRaw('scrapping_method, COUNT(*) as n')
                ->groupBy('scrapping_method')
                ->pluck('n', 'scrapping_method')
        );

        // ── AKTIVITAS per range (cached) ──
        $activity = Cache::remember("admin_activity_{$range}d_{$monthYear}", 900, function () use ($range) {
            $since = now()->subDays($range)->toDateTimeString();
            return [
                'blast_w'      => BlashDetail::where('type', 'whatsapp')->where('created_at', '>=', $since)->count(),
                'blast_e'      => BlashDetail::where('type', 'email')->where('created_at', '>=', $since)->count(),
                'scrap_maps'   => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'gmaps')->where('created_at', '>=', $since)->count(),
                'scrap_group'  => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'group')->where('created_at', '>=', $since)->count(),
                'scrap_kontak' => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'contacts')->where('created_at', '>=', $since)->count(),
            ];
        });

        // ── DATA & LOGS (existing, unchanged) ──
        $data = Cache::remember("admin_data_{$monthYear}", 900, function () {
            $thirtyDaysAgo = now()->subDays(30)->toDateTimeString();
            return [
                'stores'      => Store::count(),
                'categories'  => Category::count(),
                'blashs'      => BlashDetail::where('created_at', '>=', $thirtyDaysAgo)->where('reports', null)->count(),
                'scrapp'      => Store::where('scrapping_id', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                'sending'     => BlashDetail::where('reports', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                'not_sending' => BlashDetail::where('reports', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
            ];
        });

        $mustFollow = PackageTransaction::select('business_id', 'package_id', DB::raw('MAX(expire_date) as last_expire_date'))
            ->where('business_id', '!=', null)
            ->where('type', 'package')
            ->where('status', 'success')
            ->groupBy('business_id', 'package_id')
            ->havingRaw('MAX(expire_date) <= ?', [now()->addDays(7)->toDateString()])
            ->havingRaw('MAX(expire_date) >= ?', [now()->toDateString()])
            ->limit(10)->get();

        $merchantNotPackage = Setting::withoutGlobalScopes()
            ->where('merchant_id', '!=', null)
            ->where('created_at', '>', now()->subDays(30)->endOfDay())
            ->whereDoesntHave('transaction', function ($q) {
                $q->where('type', 'package')->where('status', 'success');
            })->limit(10)->get();

        $notPayment = PackageTransaction::where('status', 'pending')
            ->where('created_at', '>', now()->subDays(7)->endOfDay())
            ->orderBy('created_at', 'desc')->limit(10)->get();

        $merchants = Merchant::where('created_at', '>', now()->subDays(7)->endOfDay())->limit(10)->get();

        $logs = Cache::remember("admin_logs_{$monthYear}", 120, function () use ($request) {
            return [
                'email'    => $this->logsObserver->getData($request, 'email')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
                'whatsapp' => $this->logsObserver->getData($request, 'whatsapp')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
                'scrapp'   => $this->logsObserver->getData($request, 'scrapping')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
            ];
        });

        return view('admin.home', ['page' => __('page.dashboard'), 'breadcumb' => false], compact(
            'data', 'logs', 'summary', 'mustFollow', 'merchantNotPackage', 'notPayment', 'merchants',
            'channels', 'sub', 'mrr', 'mrrThisMonth', 'mrrGrowth', 'ai', 'aiCreditTotal',
            'aiTop', 'activeBiz', 'scrap', 'activity', 'range'
        ));
    }

    public function creditAiResponse()
    {
        $data = \Cache::remember("admin_credit_ai_" . date("Y-m"), 900, function () {
            return \App\Models\ChatBot\HistoryChatDetail::selectRaw("
                DATE(created_at) as date, sum(credit_using) as count
            ")
                ->whereBetween("created_at", [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])
                ->groupBy("date")
                ->orderBy("date")
                ->get();
        });

        return response()->json($data);
    }

    public function analiss(Request $request)
    {
        $senderData    = [];
        $notSenderData = [];
        $dateData      = [];

        $blashData = BlashDetail::selectRaw('LEFT(created_at, 10) as date,
        SUM(CASE WHEN reports IS NULL THEN 1 ELSE 0 END) AS sending,
        SUM(CASE WHEN reports IS NOT NULL THEN 1 ELSE 0 END) AS not_sending')
            ->where('created_at', ">=", now()->subDays(30))
            ->groupBy('date')
            ->get();

        foreach ($blashData as $blash) {
            $dateData[]    = Carbon::parse($blash->date, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->format('d, M Y');
            $senderData[]  = (int) $blash->sending;
            $notSenderData[] = (int) $blash->not_sending;
        }

        return response()->json([
            'analisis_blash' => [
                'sender'     => $senderData,
                'not_sender' => $notSenderData,
                'date'       => $dateData,
            ],
        ]);
    }
}
