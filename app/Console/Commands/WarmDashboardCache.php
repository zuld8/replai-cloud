<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ChatBot\HistoryChat;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\ChatBot\FineTunnel;
use App\Models\Blash\BlashDetail;
use App\Models\Master\Label;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * WarmDashboardCache — Pre-isi cache dashboard untuk SEMUA merchant & bisnis aktif.
 * Warming business-scoped + merchant-scoped agar user gak pernah kena cold-load.
 *
 * Cache keys yang di-warm:
 *   Merchant-scoped: home_interaction_analysis_{m}_{ym}, home_interactions_{m}_{ym}, home_analiss_{m}
 *   Business-scoped: home_summary_{m}_{b}_{ym}, home_crm_{m}_{b}, label_leads_{b},
 *                    pesan_masuk_{b}_7, pesan_masuk_{b}_30
 *
 * TTL: 900s (15 menit) — samain dengan controller agar warming selalu lebih lama dari expiry.
 * home_crm: TTL 600s di controller → warm setiap 10 menit = coverage sempurna.
 */
class WarmDashboardCache extends Command
{
    protected $signature   = 'dashboard:warm {--limit=200 : Max jumlah business yang di-warm} {--force : Paksa re-warm walau cache masih ada}';
    protected $description = 'Pre-isi cache dashboard (merchant-scoped + business-scoped) untuk semua user aktif';

    public function handle(): int
    {
        // Cegah timeout saat warming banyak merchant (query blash_details bisa > 60s tanpa ini)
        set_time_limit(0);
        \DB::statement('SET SESSION max_execution_time=0');

        $monthYear = now()->format('Y-m');
        $limit     = (int) $this->option('limit');
        $force     = (bool) $this->option('force');

        // ── Ambil SEMUA bisnis aktif dari settings (bukan users)
        // → cover semua bisnis (termasuk yang tidak punya user pasangan di tabel users)
        // settings.id = business_id, settings.merchant_id = parent merchant
        $pairs = DB::table('settings')
            ->whereNotNull('merchant_id')
            ->where('merchant_id', '!=', '')
            ->select('merchant_id', DB::raw('id as business_id'))
            ->limit($limit)
            ->get();

        $this->info("Warming cache untuk {$pairs->count()} merchant+bisnis pairs...");

        $warmedMerchant = [];  // track merchant yang sudah di-warm (jangan duplikat)
        $warmedBiz      = 0;
        $skipped        = 0;

        foreach ($pairs as $pair) {
            $merchantId = $pair->merchant_id;
            $businessId = $pair->business_id;

            // ── A. Merchant-scoped (sekali per merchant) ──────────────────
            if (!in_array($merchantId, $warmedMerchant)) {
                $warmedMerchant[] = $merchantId;

                // A1. interaction_analysis — data mingguan bulan ini
                $key1 = "home_interaction_analysis_{$merchantId}_{$monthYear}";
                if ($force || !Cache::has($key1)) {
                    try {
                        Cache::put($key1, $this->computeInteractionAnalysis($merchantId), 900);
                    } catch (\Throwable $e) { $this->warn("A1 {$merchantId}: " . $e->getMessage()); }
                }

                // A2. home_interactions — count per status bulan ini
                $key2 = "home_interactions_{$merchantId}_{$monthYear}";
                if ($force || !Cache::has($key2)) {
                    try {
                        Cache::put($key2, $this->computeInteractions($merchantId), 900);
                    } catch (\Throwable $e) { $this->warn("A2 {$merchantId}: " . $e->getMessage()); }
                }

                // A3. home_analiss — analisis blast 30 hari
                $key3 = "home_analiss_{$merchantId}";
                if ($force || !Cache::has($key3)) {
                    try {
                        Cache::put($key3, $this->computeAnaliss($merchantId), 900);
                    } catch (\Throwable $e) { $this->warn("A3 {$merchantId}: " . $e->getMessage()); }
                }
            }

            // ── B. Business-scoped ────────────────────────────────────────

            // B1. home_summary
            $keySum = "home_summary_{$merchantId}_{$businessId}_{$monthYear}";
            if ($force || !Cache::has($keySum)) {
                try {
                    Cache::put($keySum, $this->computeSummary($businessId), 900);
                } catch (\Throwable $e) { $this->warn("B1 biz {$businessId}: " . $e->getMessage()); }
            }

            // B2. home_crm — TTL 600s di controller, warm tiap 10 menit = selalu fresh
            $keyCrm = "home_crm_{$merchantId}_{$businessId}";
            if ($force || !Cache::has($keyCrm)) {
                try {
                    Cache::put($keyCrm, $this->computeCrm($businessId), 600);
                } catch (\Throwable $e) { $this->warn("B2 crm {$businessId}: " . $e->getMessage()); }
            }

            // B3. label_leads
            $keyLabel = "label_leads_{$businessId}";
            if ($force || !Cache::has($keyLabel)) {
                try {
                    Cache::put($keyLabel, $this->computeLabelLeads($businessId), 1800);
                } catch (\Throwable $e) { $this->warn("B3 label {$businessId}: " . $e->getMessage()); }
            }

            // B4. pesan_masuk — untuk 7 dan 30 hari (parameter paling umum)
            foreach ([7, 30, 90] as $days) {  // 90 hari untuk widget pesan-masuk
                $keyPm = "pesan_masuk_{$businessId}_{$days}";
                if ($force || !Cache::has($keyPm)) {
                    try {
                        Cache::put($keyPm, $this->computePesanMasuk($businessId, $days), 300);
                    } catch (\Throwable $e) { $this->warn("B4 pm{$days} {$businessId}: " . $e->getMessage()); }
                }
            }

            // B5. broadcast_status — 5 broadcast terbaru + aggregasi (dulu 55 detik, sekarang <1s)
            $keyBs = "broadcast_status_{$merchantId}_{$businessId}";
            if ($force || !Cache::has($keyBs)) {
                try {
                    Cache::put($keyBs, $this->computeBroadcastStatus($businessId), 600);
                } catch (\Throwable $e) { $this->warn("B5 bs {$businessId}: " . $e->getMessage()); }
            }

            // B6. broadcast_summary — untuk days=7,30,90
            foreach ([7, 30, 90] as $days) {
                $keyBsum = "broadcast_summary_{$businessId}_{$days}";
                if ($force || !Cache::has($keyBsum)) {
                    try {
                        // broadcast_summary punya query sendiri — skip jika method belum ada
                        // Cache::put($keyBsum, $this->computeBroadcastSummary($businessId, $days), 300);
                    } catch (\Throwable $e) {}
                }
            }

            // B7. storage_usage — SELALU overwrite agar TTL tidak decay ke 0 (cegah badge 0 sesaat)
            // computeStorage = du -sb → murah, aman dijalankan tiap 10 menit warm
            $keySt = "storage_usage_business_{$businessId}";
            try {
                Cache::put($keySt, $this->computeStorage($businessId), 1800); // 30 menit
            } catch (\Throwable $e) { $this->warn("B7 storage {$businessId}: " . $e->getMessage()); }

            $warmedBiz++;
        }

        $this->info("✅ Done! Warmed {$warmedBiz} bisnis, " . count($warmedMerchant) . " merchant unik. Admin keys di-warm oleh dashboard:warm-admin.");
        return self::SUCCESS;    }
}
