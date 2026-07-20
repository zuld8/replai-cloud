<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ChatBot\HistoryChat;
use App\Models\Blash\BlashDetail;
use App\Models\Master\Label;
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

        // ── Ambil semua pasangan (merchant_id, business_id) aktif dari users ──
        // users.business_id menyimpan business_id (= settings.id)
        $pairs = DB::table('users')
            ->whereNotNull('merchant_id')
            ->whereNotNull('business_id')
            ->where('business_id', '!=', '')
            ->select('merchant_id', 'business_id')
            ->distinct()
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

            $warmedBiz++;
        }

        $this->info("✅ Done! Warmed {$warmedBiz} bisnis, " . count($warmedMerchant) . " merchant unik.");
        return self::SUCCESS;
    }

    // ── Query methods (single source of truth, sama dengan HomeController) ──

    private function computeInteractionAnalysis(string $merchantId): mixed
    {
        // FIX P0 addendum: filter merchant eksplisit (CLI tanpa session → global scope no-op)
        return HistoryChat::withoutGlobalScopes()
            ->where('merchant_id', $merchantId)
            ->selectRaw("
            YEARWEEK(created_at, 1) as yearweek,
            MIN(DATE(created_at)) as start_date,
            COUNT(*) as count
        ")
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupBy('yearweek')
            ->orderBy('start_date')
            ->get();
    }

    private function computeInteractions(string $merchantId): mixed
    {
        // FIX P0 addendum: filter merchant eksplisit
        return HistoryChat::withoutGlobalScopes()
            ->where('merchant_id', $merchantId)
            ->selectRaw('status, COUNT(*) as total')
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupBy('status')
            ->get();
    }

    private function computeAnaliss(string $merchantId): array
    {
        $senderData = $notSenderData = $dateData = [];

        $blashData = BlashDetail::whereHas('parent', function ($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            })
            ->selectRaw('LEFT(created_at, 10) as date,
                SUM(CASE WHEN reports IS NULL THEN 1 ELSE 0 END) AS sending,
                SUM(CASE WHEN reports IS NOT NULL THEN 1 ELSE 0 END) AS not_sending')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        foreach ($blashData as $b) {
            $dateData[]      = Carbon::parse($b->date, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->format('d, M Y');
            $senderData[]    = (int) $b->sending;
            $notSenderData[] = (int) $b->not_sending;
        }

        return ['analisis_blash' => ['sender' => $senderData, 'not_sender' => $notSenderData, 'date' => $dateData]];
    }

    private function computeSummary(string $businessId): array
    {
        // FIX: return array LENGKAP sesuai home() — semua key yang dipakai home.blade.php
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();
        $since30    = now()->subDays(30);

        // Ambil id broadcast bisnis ini SEKALI (ratusan, bukan jutaan)
        $bw       = DB::table('blash_whatsapps')->where('business_id', $businessId)->get(['id', 'use']);
        $waIds    = $bw->where('use', 'whatsapp')->pluck('id')->all();
        $emailIds = $bw->where('use', 'email')->pluck('id')->all();
        $allIds   = $bw->pluck('id')->all();

        $blastW = empty($waIds)    ? 0 : BlashDetail::whereIn('blash_whatsapp_id', $waIds)
                      ->whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $blastE = empty($emailIds) ? 0 : BlashDetail::whereIn('blash_whatsapp_id', $emailIds)
                      ->whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $snd = empty($allIds) ? null : DB::table('blash_details')
            ->whereIn('blash_whatsapp_id', $allIds)
            ->where('created_at', '>=', $since30)
            ->selectRaw('SUM(reports IS NULL) AS sending, SUM(reports IS NOT NULL) AS not_sending')
            ->first();

        // Return WAJIB lengkap — semua key yang dipakai home.blade.php
        // FIX P0: eksplisit where business_id — DB::table() tidak kena global scope,
        //          jalan di CLI (tanpa session) → tanpa filter = count semua bisnis (tenant leak)
        return [
            'unofficial'  => DB::table('whatsapp_devices')->where('business_id', $businessId)->count(),
            'official'    => DB::table('whatsapp_key_accounts')->where('business_id', $businessId)->count(),
            'livechats'   => DB::table('live_chats')->where('business_id', $businessId)->count(),
            'telegram'    => DB::table('telegram_keys')->where('business_id', $businessId)->count(),
            'instagram'   => DB::table('instagram_accounts')->where('business_id', $businessId)->count(),
            'messenger'   => DB::table('messenger_accounts')->where('business_id', $businessId)->count(),
            'finetunnels' => DB::table('fine_tunnels')->where('business_id', $businessId)->count(),
            'stores'      => DB::table('stores')->where('business_id', $businessId)->count(),
            'categories'  => DB::table('categories')->where('business_id', $businessId)->count(),
            'user'        => DB::table('users')->where('business_id', $businessId)->count(),
            'blast_w'     => $blastW,
            'blast_e'     => $blastE,
            'scraping'    => DB::table('stores')->where('business_id', $businessId)
                                ->whereNotNull('scrapping_id')
                                ->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'sending'     => (int) ($snd->sending     ?? 0),
            'not_sending' => (int) ($snd->not_sending ?? 0),
        ];
    }

    private function computeCrm(string $businessId): array
    {
        // 5 newest unread — pakai index idx_hc_crm_unread (business_id, status, unread_count, last_message_at)
        $newest = HistoryChat::where('business_id', $businessId)
            ->where('unread_count', '>', 0)
            ->whereIn('status', ['open', 'pending'])
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count']);

        // 5 oldest unread
        $oldest = HistoryChat::where('business_id', $businessId)
            ->where('unread_count', '>', 0)
            ->whereIn('status', ['open', 'pending'])
            ->orderBy('last_message_at', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'from_number', 'from', 'status', 'last_message_at', 'unread_count', 'created_at']);

        return ['newest' => $newest, 'oldest' => $oldest];
    }

    private function computeLabelLeads(string $businessId): array
    {
        $allLabelJson = HistoryChat::where('business_id', $businessId)
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

        $labels = Label::where('business_id', $businessId)
            ->select('id', 'name', 'color')
            ->get()
            ->map(fn($label) => [
                'label' => $label->name,
                'data'  => $counts[$label->id] ?? 0,
                'color' => $label->color ?? '#0EA5E9',
            ])
            ->filter(fn($item) => $item['data'] > 0)
            ->values();

        return ['labels' => $labels];
    }

    private function computeBroadcastStatus(string $businessId): array
    {
        // Step 1: Ambil 5 broadcast terbaru (ringan, tidak sentuh blash_details)
        $broadcasts = DB::select("
            SELECT id, name, `use`, created_at
            FROM blash_whatsapps
            WHERE business_id = ?
            ORDER BY created_at DESC
            LIMIT 5
        ", [$businessId]);

        if (empty($broadcasts)) return [];

        $ids          = array_map(fn($b) => $b->id, $broadcasts);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Step 2: Agregasi HANYA untuk 5 broadcast itu
        $totalsRaw = DB::select("
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
            $result[] = [
                'id'         => $b->id,
                'name'       => $b->name,
                'use'        => $b->use,
                'total'      => $total,
                'sent'       => $sent,
                'failed'     => $failed,
                'rate'       => $total > 0 ? round($sent / $total * 100, 1) : 0,
                'created_at' => $b->created_at,
                'devices'    => [],  // devices di-lazy load saat user klik detail
            ];
        }
        return $result;
    }

    private function computePesanMasuk(string $businessId, int $days): array
    {
        $startDate = now()->subDays($days)->startOfDay()->toDateTimeString();
        $endDate   = now()->endOfDay()->toDateTimeString();

        $data = DB::select("
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

        $dates = $totals = [];
        $grandTotal = 0;
        foreach ($data as $row) {
            $dates[]  = Carbon::parse($row->date)->format('d M');
            $totals[] = (int) $row->total;
            $grandTotal += (int) $row->total;
        }

        return ['dates' => $dates, 'totals' => $totals, 'grand_total' => $grandTotal];
    }
}
