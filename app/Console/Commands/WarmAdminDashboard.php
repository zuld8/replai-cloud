<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
// ── Models (sinkron dgn HomeController imports) ──
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

/**
 * WarmAdminDashboard — Pre-isi SEMUA cache key halaman /administrator.
 * Dijalankan tiap 5 menit via cron (schedule:run).
 *
 * TTL semua key: 1800s (30 mnt).
 * Schedule interval: 5 mnt.
 * Hasil: /administrator selalu < 500ms (cache HIT untuk semua blok home()).
 *
 * PENTING: query di sini HARUS sinkron dgn home() di HomeController.
 * Jika ada perubahan query/key di home() → update sini juga.
 */
class WarmAdminDashboard extends Command
{
    protected $signature   = 'dashboard:warm-admin {--force : Paksa re-warm walau cache masih ada}';
    protected $description = 'Pre-isi SEMUA cache key dashboard admin (home + AJAX widgets)';

    public function handle(): int
    {
        set_time_limit(0);
        DB::statement('SET SESSION max_execution_time=0');

        $force      = (bool) $this->option('force');
        $monthYear  = now()->format('Y-m');
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $monthEnd   = now()->endOfMonth()->toDateTimeString();

        // ────────────────────────────────────────────────────────────
        // BLOK A — Key halaman home() (sinkron dgn HomeController::home)
        // ────────────────────────────────────────────────────────────

        $homeJobs = [

            // ── 1. SUMMARY: KPI utama ──
            // SINKRON dgn: Cache::remember("admin_summary_{$monthYear}", 900, ...)
            "admin_summary_{$monthYear}" => function () use ($monthStart, $monthEnd) {
                return [
                    'merchants'   => Merchant::count(),
                    'business'    => Setting::withoutGlobalScopes()->whereNotNull('merchant_id')->count(),
                    'packages'    => PackageTransaction::where('status', 'success')->where('type', 'package')
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                    'topup'       => PackageTransaction::where('status', 'success')->where('type', 'topup')
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                    'finetunnels' => FineTunnel::withoutGlobalScopes()->count(),
                    'users'       => User::withoutGlobalScopes()->count(),
                    'devices'     => WhatsappDevice::withoutGlobalScopes()->count(),
                    'blast_w'     => BlashDetail::where('type', 'whatsapp')
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'blast_e'     => BlashDetail::where('type', 'email')
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'scraping'    => Store::where('scrapping_id', '!=', null)
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                ];
            },

            // ── 2. CHANNEL: jumlah per platform ──
            // SINKRON dgn: Cache::remember("admin_channels_{$monthYear}", 900, ...)
            "admin_channels_{$monthYear}" => fn () => [
                'waba'      => WhatsappKeyAccount::withoutGlobalScopes()->count(),
                'wa_pers'   => WhatsappDevice::withoutGlobalScopes()->count(),
                'instagram' => InstagramAccount::withoutGlobalScopes()->count(),
                'messenger' => MessengerAccount::withoutGlobalScopes()->count(),
                'telegram'  => TelegramKey::withoutGlobalScopes()->count(),
                'livechat'  => LiveChatModel::withoutGlobalScopes()->count(),
            ],

            // ── 3. SUBSCRIPTION HEALTH ──
            // SINKRON dgn: Cache::remember("admin_sub_health_{$monthYear}", 900, ...)
            "admin_sub_health_{$monthYear}" => function () {
                $totalBiz = Setting::withoutGlobalScopes()->whereNotNull('merchant_id')->count();
                $aktif    = PackageTransaction::where('status', 'success')
                    ->where('type', 'package')
                    ->where('expire_date', '>=', now())
                    ->distinct('business_id')->count('business_id');
                return [
                    'total'       => $totalBiz,
                    'aktif'       => $aktif,
                    'tanpa_paket' => max(0, $totalBiz - $aktif),
                    'konversi'    => $totalBiz > 0 ? round($aktif / $totalBiz * 100) : 0,
                ];
            },

            // ── 4. MRR 12 bulan ──
            // SINKRON dgn: Cache::remember("admin_mrr_12m", 900, ...)
            "admin_mrr_12m" => fn () =>
                PackageTransaction::where('status', 'success')
                    ->where('type', 'package')
                    ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                    ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as ym, SUM(final_total) as total")
                    ->groupBy('ym')->orderBy('ym')->get(),

            // ── 5. SCRAPING per method ──
            // SINKRON dgn: Cache::remember("admin_scrap_{$monthYear}", 900, ...)
            "admin_scrap_{$monthYear}" => fn () =>
                Scrapping::withoutGlobalScopes()
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->selectRaw('scrapping_method, COUNT(*) as n')
                    ->groupBy('scrapping_method')
                    ->pluck('n', 'scrapping_method'),

            // ── 6. AKTIVITAS range=30 (default) ──
            // SINKRON dgn: Cache::remember("admin_activity_{$range}d_{$monthYear}", 900, ...)
            "admin_activity_30d_{$monthYear}" => function () {
                $since = now()->subDays(30)->toDateTimeString();
                return [
                    'blast_w'      => BlashDetail::where('type', 'whatsapp')->where('created_at', '>=', $since)->count(),
                    'blast_e'      => BlashDetail::where('type', 'email')->where('created_at', '>=', $since)->count(),
                    'scrap_maps'   => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'gmaps')->where('created_at', '>=', $since)->count(),
                    'scrap_group'  => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'group')->where('created_at', '>=', $since)->count(),
                    'scrap_kontak' => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'contacts')->where('created_at', '>=', $since)->count(),
                ];
            },

            // ── 7. AKTIVITAS range=7 (tab alternatif) ──
            "admin_activity_7d_{$monthYear}" => function () {
                $since = now()->subDays(7)->toDateTimeString();
                return [
                    'blast_w'      => BlashDetail::where('type', 'whatsapp')->where('created_at', '>=', $since)->count(),
                    'blast_e'      => BlashDetail::where('type', 'email')->where('created_at', '>=', $since)->count(),
                    'scrap_maps'   => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'gmaps')->where('created_at', '>=', $since)->count(),
                    'scrap_group'  => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'group')->where('created_at', '>=', $since)->count(),
                    'scrap_kontak' => Scrapping::withoutGlobalScopes()->where('scrapping_method', 'contacts')->where('created_at', '>=', $since)->count(),
                ];
            },

            // ── 8. DATA & KONTAK ──
            // SINKRON dgn: Cache::remember("admin_data_{$monthYear}", 900, ...)
            "admin_data_{$monthYear}" => function () {
                $thirtyDaysAgo = now()->subDays(30)->toDateTimeString();
                return [
                    'stores'      => Store::count(),
                    'categories'  => Category::count(),
                    'blashs'      => BlashDetail::where('created_at', '>=', $thirtyDaysAgo)->where('reports', null)->count(),
                    'scrapp'      => Store::where('scrapping_id', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                    'sending'     => BlashDetail::where('reports', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                    'not_sending' => BlashDetail::where('reports', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                ];
            },

            // ── 9. CHURN RISK ──
            // SINKRON dgn: Cache::remember("admin_churn_{$monthYear}", 900, ...)
            "admin_churn_{$monthYear}" => function () {
                $paidBizIds = PackageTransaction::where('status', 'success')
                    ->where('type', 'package')
                    ->where('expire_date', '>=', now())
                    ->whereHas('package', fn($q) => $q->where('price', '>', 0))
                    ->pluck('business_id')->unique()->values();
                if ($paidBizIds->isEmpty()) return 0;
                $aktif3d = HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
                    ->whereIn('history_chats.business_id', $paidBizIds->all())
                    ->where('history_chat_details.created_at', '>=', now()->subDays(3))
                    ->distinct('history_chats.business_id')
                    ->count('history_chats.business_id');
                return max(0, $paidBizIds->count() - $aktif3d);
            },

        ]; // end homeJobs

        // ────────────────────────────────────────────────────────────
        // BLOK B — Key AJAX widgets (wAiStats, wActiveBiz, creditAiResponse)
        // ────────────────────────────────────────────────────────────

        $ajaxJobs = [

            // ── 10. AI USAGE ──
            "admin_ai_usage_{$monthYear}" => function () use ($monthStart, $monthEnd) {
                $row = DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
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
            },

            // ── 11. AI CREDIT TOTAL ──
            "admin_credit_ai_total_{$monthYear}" => fn () =>
                DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('credit_using'),

            // ── 12. AI TOP 5 per bisnis ──
            "admin_ai_top_{$monthYear}" => fn () =>
                HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
                    ->whereBetween('history_chat_details.created_at', [$monthStart, $monthEnd])
                    ->where('history_chat_details.source', 'bot')
                    ->selectRaw('history_chats.business_id, COUNT(*) as n')
                    ->groupBy('history_chats.business_id')
                    ->orderByDesc('n')->limit(5)->get(),

            // ── 13. BISNIS AKTIF 7 hari (history_chats, FORCE INDEX) ──
            "admin_active_biz" => fn () =>
                DB::table(DB::raw('`history_chats` FORCE INDEX (`idx_last_message_at`)'))
                    ->where('last_message_at', '>=', now()->subDays(7))
                    ->whereNotNull('business_id')
                    ->selectRaw('business_id, MAX(last_message_at) as last_activity, COUNT(*) as chat_7d')
                    ->groupBy('business_id')
                    ->orderByDesc('last_activity')->limit(10)->get(),

            // ── 14. AI CREDIT CHART harian ──
            "admin_credit_ai_{$monthYear}" => fn () =>
                DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
                    ->selectRaw("DATE(created_at) as date, sum(credit_using) as count")
                    ->whereBetween("created_at", [$monthStart, $monthEnd])
                    ->groupBy("date")->orderBy("date")->get(),


            // ── 15. AI TOKEN TRACKING (total raw token platform) ──
            "admin_ai_tokens_{$monthYear}" => fn () => [
                'today' => HistoryChatDetail::whereDate('created_at', today())
                    ->where('total_tokens', '>', 0)->sum('total_tokens'),
                'bulan' => HistoryChatDetail::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('total_tokens', '>', 0)->sum('total_tokens'),
                'chart' => HistoryChatDetail::where('total_tokens', '>', 0)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->selectRaw('DATE(created_at) as tgl, SUM(total_tokens) as total')
                    ->groupBy('tgl')->orderBy('tgl')->get(),
            ],

        ]; // end ajaxJobs

        // ────────────────────────────────────────────────────────────
        // Eksekusi semua jobs (home dulu, AJAX kemudian)
        // ────────────────────────────────────────────────────────────

        $allJobs = array_merge($homeJobs, $ajaxJobs);
        $warmed  = 0;

        foreach ($allJobs as $key => $fn) {
            if ($force || !Cache::has($key)) {
                try {
                    Cache::put($key, $fn(), 1800);
                    $this->line("  ✓ {$key}");
                    $warmed++;
                } catch (\Throwable $e) {
                    $this->warn("  ✗ {$key}: " . $e->getMessage());
                }
            } else {
                $this->line("  · skip (hit): {$key}");
            }
        }

        $this->info("✅ Admin warm selesai — {$warmed} key di-refresh dari " . count($allJobs) . " total.");
        return self::SUCCESS;
    }
}
