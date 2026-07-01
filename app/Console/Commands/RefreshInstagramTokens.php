<?php

namespace App\Console\Commands;

use App\Models\Meta\InstagramAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Refresh long-lived Instagram tokens (IG-Login, graph.instagram.com).
 * Hanya refresh token yang sisa umurnya <= --days (default 10 hari).
 * Token yang sudah expired (tidak bisa di-refresh) → set status = expired.
 *
 * Jadwal: harian 03:00 WIB (routes/console.php).
 * Manual: php artisan instagram:refresh-tokens [--days=N]
 *         php artisan instagram:refresh-tokens --days=9999 (paksa semua)
 */
class RefreshInstagramTokens extends Command
{
    protected $signature   = 'instagram:refresh-tokens {--days=10 : refresh kalau sisa umur <= N hari}';
    protected $description = 'Refresh long-lived Instagram token yang mendekati expired';

    public function handle(): int
    {
        $threshold = now()->addDays((int) $this->option('days'));
        $refreshed = 0;
        $failed    = 0;
        $skipped   = 0;

        $accounts = InstagramAccount::whereIn('status', ['active', 'expired'])
            ->whereNotNull('access_token')
            ->get();

        foreach ($accounts as $acc) {
            // Skip akun FB-Login (punya page_id) — token FB dikelola Meta Graph, gak pakai endpoint ini
            if (!empty($acc->page_id)) {
                $skipped++;
                continue;
            }

            $exp = $acc->token_expires_at ? \Carbon\Carbon::parse($acc->token_expires_at) : null;

            // 1) Sudah lewat expired → tidak bisa di-refresh, wajib reconnect
            if ($exp && $exp->isPast()) {
                if ($acc->status !== 'expired') {
                    $acc->update(['status' => 'expired']);
                }
                Log::warning('[IG Token] EXPIRED — butuh reconnect', [
                    'ig' => $acc->instagram_id, 'username' => $acc->username,
                    'expired_at' => $exp->toDateTimeString(),
                ]);
                $failed++;
                continue;
            }

            // 2) Masih jauh dari expired → skip (hemat API call)
            if ($exp && $exp->greaterThan($threshold)) {
                $skipped++;
                continue;
            }

            // 3) Mendekati expired (atau expiry tak diketahui) → refresh sekarang
            $resp = Http::timeout(15)->get('https://graph.instagram.com/refresh_access_token', [
                'grant_type'   => 'ig_refresh_token',
                'access_token' => $acc->access_token,
            ]);

            if ($resp->successful() && ($tok = $resp->json('access_token'))) {
                $newExpiry = now()->addSeconds($resp->json('expires_in') ?? 5184000); // 60 hari
                $acc->update([
                    'access_token'     => $tok,
                    'token_expires_at' => $newExpiry,
                    'status'           => 'active',   // pastikan aktif kembali
                ]);
                $refreshed++;
                Log::info('[IG Token] Refreshed OK', [
                    'ig' => $acc->instagram_id, 'username' => $acc->username,
                    'new_expiry' => $newExpiry->toDateTimeString(),
                ]);
                $this->line("  ✅ Refreshed: @{$acc->username} → expires {$newExpiry->toDateString()}");
            } else {
                // Refresh gagal: token sudah dicabut / password diganti / <24 jam → tandai perlu reconnect
                $acc->update(['status' => 'expired']);
                $failed++;
                Log::warning('[IG Token] Refresh FAILED — butuh reconnect', [
                    'ig'       => $acc->instagram_id,
                    'username' => $acc->username,
                    'http'     => $resp->status(),
                    'body'     => $resp->body(),
                ]);
                $this->warn("  ❌ FAILED: @{$acc->username} — {$resp->json('error.message', 'unknown')}");
            }
        }

        $this->info(
            "IG refresh selesai — refreshed:{$refreshed} skipped:{$skipped} failed:{$failed}"
        );
        return self::SUCCESS;
    }
}
