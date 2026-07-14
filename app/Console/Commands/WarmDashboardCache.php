<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ChatBot\HistoryChat;
use App\Models\Blash\BlashDetail;
use Carbon\Carbon;

/**
 * WarmDashboardCache — pre-isi cache dashboard untuk semua merchant aktif.
 * Jalankan tiap 10 menit via scheduler agar user tidak pernah kena cold-load 1 menit.
 */
class WarmDashboardCache extends Command
{
    protected $signature   = 'dashboard:warm {--limit=100 : Max jumlah merchant yang di-warm}';
    protected $description = 'Pre-isi cache widget dashboard (interaksi, analisis blast) untuk semua merchant aktif';

    public function handle(): int
    {
        $monthYear = now()->format('Y-m');
        $limit     = (int) $this->option('limit');

        // Ambil daftar merchant_id unik yang punya bisnis (settings)
        $merchantIds = DB::table('users')
            ->whereNotNull('merchant_id')
            ->distinct()
            ->limit($limit)
            ->pluck('merchant_id');

        $this->info("Warming cache untuk {$merchantIds->count()} merchant...");
        $warmed = 0;

        foreach ($merchantIds as $merchantId) {
            try {
                // 1. Interaction Analysis — data mingguan bulan ini (berat, tanpa filter business)
                $cacheKey1 = "home_interaction_analysis_{$merchantId}_{$monthYear}";
                if (!Cache::has($cacheKey1)) {
                    Cache::put($cacheKey1, HistoryChat::selectRaw("
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
                        ->get(), 900);
                }

                // 2. Interactions bulanan — selectRaw status count
                $cacheKey2 = "home_interactions_{$merchantId}_{$monthYear}";
                if (!Cache::has($cacheKey2)) {
                    Cache::put($cacheKey2, HistoryChat::selectRaw('status, COUNT(*) as total')
                        ->whereBetween('created_at', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        ])
                        ->groupBy('status')
                        ->get(), 900);
                }

                // 3. Analiss blast 30 hari
                $cacheKey3 = "home_analiss_{$merchantId}";
                if (!Cache::has($cacheKey3)) {
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

                    Cache::put($cacheKey3, [
                        'analisis_blash' => [
                            'sender'     => $senderData,
                            'not_sender' => $notSenderData,
                            'date'       => $dateData,
                        ],
                    ], 900);
                }

                $warmed++;
            } catch (\Throwable $e) {
                $this->warn("Skip merchant {$merchantId}: " . $e->getMessage());
            }
        }

        $this->info("Cache warmed untuk {$warmed}/{$merchantIds->count()} merchant.");
        return self::SUCCESS;
    }
}
