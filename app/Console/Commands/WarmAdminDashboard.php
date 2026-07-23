<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\ChatBot\FineTunnel;

/**
 * WarmAdminDashboard — Pre-isi cache 5 widget berat dashboard admin.
 * Ringan (~6 query), independen dari loop 200 bisnis di WarmDashboardCache.
 * Dijadwal tiap 5 menit. TTL 1800s >> interval 5 mnt → selalu HIT.
 *
 * Keys yang di-warm (PERSIS sama dengan HomeController):
 *   admin_ai_usage_{Y-m}         → wAiStats()
 *   admin_credit_ai_total_{Y-m}  → wAiStats()
 *   admin_ai_top_{Y-m}           → wAiStats()
 *   admin_active_biz             → wActiveBiz()
 *   admin_credit_ai_{Y-m}        → creditAiResponse()
 */
class WarmAdminDashboard extends Command
{
    protected $signature   = 'dashboard:warm-admin {--force : Paksa re-warm walau cache masih ada}';
    protected $description = 'Pre-isi cache widget berat dashboard admin (ai-stats, active-biz, response-ai)';

    public function handle(): int
    {
        set_time_limit(0);
        DB::statement('SET SESSION max_execution_time=0');

        $force      = (bool) $this->option('force');
        $monthYear  = now()->format('Y-m');
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $monthEnd   = now()->endOfMonth()->toDateTimeString();

        $adminJobs = [
            // ── 1. AI Usage (ai-stats: ai_replies, automation%, training) ──
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

            // ── 2. AI Credit Total (angka di kartu) ──
            "admin_credit_ai_total_{$monthYear}" => fn () =>
                DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('credit_using'),

            // ── 3. AI Top 5 per bisnis ──
            "admin_ai_top_{$monthYear}" => fn () =>
                HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
                    ->whereBetween('history_chat_details.created_at', [$monthStart, $monthEnd])
                    ->where('history_chat_details.source', 'bot')
                    ->selectRaw('history_chats.business_id, COUNT(*) as n')
                    ->groupBy('history_chats.business_id')
                    ->orderByDesc('n')->limit(5)->get(),

            // ── 4. Bisnis aktif terkini (active-biz, 7 hari) ──
            // OPTIMASI: pakai history_chats.last_message_at (1 row/conversation, bukan per-pesan)
            // 111k rows (7 hari) vs 269k detail rows → jauh lebih ringan. Index idx_last_message_at dipakai.
            "admin_active_biz" => fn () =>
                DB::table(DB::raw('`history_chats` FORCE INDEX (`idx_last_message_at`)'))
                    ->where('last_message_at', '>=', now()->subDays(7))
                    ->whereNotNull('business_id')
                    ->selectRaw('business_id, MAX(last_message_at) as last_activity, COUNT(*) as chat_7d')
                    ->groupBy('business_id')
                    ->orderByDesc('last_activity')->limit(10)->get(),

            // ── 5. AI Credit Chart harian (response-ai) ──
            "admin_credit_ai_{$monthYear}" => fn () =>
                DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
                    ->selectRaw("DATE(created_at) as date, sum(credit_using) as count")
                    ->whereBetween("created_at", [$monthStart, $monthEnd])
                    ->groupBy("date")->orderBy("date")->get(),
        ];

        $warmed = 0;
        foreach ($adminJobs as $key => $fn) {
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

        $this->info("✅ Admin warm selesai — {$warmed} key di-refresh.");
        return self::SUCCESS;
    }
}
