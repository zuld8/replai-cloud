<?php

use App\Console\Commands\RefreshInstagramTokens;
use App\Jobs\BlashWhatsappGroupJob;
use App\Jobs\FineTunnelStatusCheckJob;
use App\Jobs\FollowUpJob;
use App\Jobs\NonActiveDeviceExpiredJob;
use App\Jobs\ResetTokenAiJob;
use App\Jobs\ResetWhatsappDailySendJob;
use App\Jobs\ScrappingContactOnGroupJob;
use App\Jobs\ScrappingGmapsJob;
use App\Jobs\ScrappingGroupJob;
use App\Jobs\ScrappingWhatsappContactJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SendPromotionEmailJob;
use App\Jobs\SendUpsellCampaignJob;
use App\Jobs\SendWhatsappJob;
use App\Jobs\UpsellCampaignSchedulerJob; 
use App\Models\Blash\BlashWhatsapp;
use App\Models\Store\Scrapping;
use App\Models\Store\WhatsappGroup;
use Illuminate\Support\Facades\Schedule;

// Clear stale broadcast:creating locks (10-min TTL sometimes causes stuck broadcasts)
Schedule::call(function () {
    try {
        $redis = \Illuminate\Support\Facades\Redis::connection();
        $keys = $redis->keys('*broadcast:creating*');
        foreach ($keys as $key) {
            $ttl = $redis->ttl($key);
            // Delete if no expiry (crashed) or been running > 8 min (stuck)
            if ($ttl === -1 || $ttl > 120) {
                $redis->del($key);
                \Illuminate\Support\Facades\Log::info("Cleared stuck broadcast:creating lock: {$key}");
            }
        }
    } catch (\Throwable $e) {
        // silently ignore
    }
})->everyMinute()->name('clear-broadcast-locks')->withoutOverlapping(1);

// Dispatch to background queue - job runs fast (dispatches batches to Horizon)
// withoutOverlapping(2) prevents duplicate runs within 2 min window
Schedule::job(new SendWhatsappJob)
    ->everyMinute()
    ->withoutOverlapping(2)
    ->when(function () {
        return BlashWhatsapp::withoutGlobalScopes()
            ->where('status', 'pending')
            ->where("use", "whatsapp")
            ->where('schedule', '<=', now())
            ->exists();
    });

// RESCUE: Catch broadcasts that cron missed (overdue >2min, 0 details)
// Runs every minute as safety net
Schedule::call(function () {
    $overdue = \App\Models\Blash\BlashWhatsapp::withoutGlobalScopes()
        ->where('status', 'pending')
        ->where('use', 'whatsapp')
        ->where('schedule', '<=', now()->subMinutes(2))
        ->whereDoesntHave('details')
        ->count();
    if ($overdue > 0) {
        // Clear any stale locks for overdue broadcasts
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            foreach ($redis->keys('*broadcast:creating*') as $key) {
                if ($redis->ttl($key) < 0 || $redis->ttl($key) > 55) {
                    $redis->del($key);
                }
            }
        } catch (\Throwable $e) {}
        \App\Jobs\SendWhatsappJob::dispatch();
        \Illuminate\Support\Facades\Log::warning("RescuePending: {$overdue} overdue broadcasts, dispatched SendWhatsappJob");
    }
})
    ->everyMinute()
    ->name('rescue-pending-broadcasts')
    ->withoutOverlapping(1);

Schedule::job(new BlashWhatsappGroupJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->when(function () {
        return BlashWhatsapp::where("use", "whatsapp_group")
            ->where("status", "pending")
            ->where("schedule", "<=", now())
            ->exists();
    });

Schedule::job(new ScrappingWhatsappContactJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->when(function () {
        return Scrapping::where("status", "pending")
            ->where("schedule", "<=", now())
            ->where('scrapping_method', 'contacts')
            ->exists();
    });

Schedule::job(new ScrappingGroupJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->when(function () {
        return Scrapping::where("status", "pending")
            ->where("schedule", "<=", now())
            ->where('scrapping_method', 'group')
            ->exists();
    });

// Scraping kontak dari member grup WhatsApp
// Proses satu grup per menit, Node.js callback ke /api-app/scrapping/callback/{id}/{business}
Schedule::job(new ScrappingContactOnGroupJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->when(function () {
        return WhatsappGroup::withoutGlobalScopes()
            ->where('scraping', 'yes')
            ->exists();
    });
    

// Auto-register pending WABA phone numbers (every 10 minutes)
Schedule::command('waba:auto-register')
    ->everyTenMinutes()
    ->withoutOverlapping(5)
    ->runInBackground();


// Auto-redispatch broadcasts stuck in processing/queued (every 5 minutes)
Schedule::command('broadcast:redispatch-stuck')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->runInBackground();

// Auto-mark completed broadcasts every 5 minutes
Schedule::command('broadcast:mark-completed')->everyFiveMinutes();

// Refresh broadcast stat cache daily at 3:30am
Schedule::command('broadcast:refresh-stats')->everyThirtyMinutes();

// Accurate recompute every hour (mutually-exclusive funnel — more precise than refresh-stats)
Schedule::command('broadcast:recompute-stats --limit=200')
    ->hourly()
    ->withoutOverlapping(20)
    ->runInBackground();

// Auto-cleanup: remove old failed_jobs (keep only 3 days)
Schedule::call(function () {
    \Illuminate\Support\Facades\DB::table('failed_jobs')
        ->where('failed_at', '<', now()->subDays(3))
        ->delete();
})->dailyAt('03:30')->name('cleanup-failed-jobs')->withoutOverlapping();

// Auto-cleanup: hapus logs table > 90 hari (tiap hari jam 03:30)
Schedule::call(function () {
    try {
        $deleted = \DB::table('logs')->where('created_at', '<', now()->subDays(90))->delete();
        \Illuminate\Support\Facades\Log::info("Cleanup logs table: {$deleted} rows deleted");
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::warning("Cleanup logs table failed: " . $e->getMessage());
    }
})->dailyAt('03:30')->name('cleanup:logs-table')->withoutOverlapping(30);

// Auto-cleanup: history_chat_details > 6 bulan (tiap hari jam 04:30, batch 5000)
Schedule::call(function () {
    try {
        $deleted = \DB::table('history_chat_details')
            ->where('created_at', '<', now()->subMonths(6))
            ->limit(5000)
            ->delete();
        if ($deleted > 0) {
            \Illuminate\Support\Facades\Log::info("Cleanup history_chat_details: {$deleted} rows deleted");
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::warning("Cleanup history_chat_details failed: " . $e->getMessage());
    }
})->dailyAt('04:30')->name('cleanup:history-chat-details')->withoutOverlapping(30);

// ─── Weekly DB purge (Sunday 04:00 — safe, no lock on InnoDB) ───
Schedule::command('crm:purge-old-data --months=6 --force')
    ->weekly()
    ->sundays()
    ->at('04:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/purge-old-data.log'));

// Every 10 min: reset blash_details stuck in 'sending' (worker crashed mid-flight)
Schedule::command('broadcast:reset-stuck --minutes=45') // 45min > max job runtime (1800s+buffer)
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Reset daily send counters at midnight WIB
Schedule::job(new ResetWhatsappDailySendJob)
    ->dailyAt('00:00')
    ->name('reset-daily-send')
    ->withoutOverlapping();

// ── Instagram token auto-refresh ──────────────────────────────────────────
// Refresh IG-Login tokens setiap hari pukul 03:00 WIB (threshold: sisa <=10 hari)
Schedule::command(RefreshInstagramTokens::class, ['--days' => 10])
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->name('instagram:refresh-tokens');

// ── Reminder paket akan/sudah berakhir — harian jam 08:00 WIB ───────────────
Schedule::job(new \App\Jobs\ExpiryReminderJob)
    ->dailyAt('08:00')
    ->name('reminder-paket')
    ->withoutOverlapping();

// Pre-warm dashboard cache tiap 10 menit — cegah cold-load 1 menit+ untuk semua merchant
Schedule::command('dashboard:warm')
    ->everyTenMinutes()
    ->withoutOverlapping(15) // naikkan ke 15 mnt — warm 57 bisnis bisa 5-10 mnt
    ->runInBackground()
    ->name('dashboard-warm');

// Pre-warm admin dashboard widgets (ai-stats, active-biz, response-ai) tiap 5 menit.
// Command ringan (~6 query), independen dari loop bisnis. TTL 1800 >> 5 mnt → selalu HIT.
Schedule::command('dashboard:warm-admin')
    ->everyFiveMinutes()
    ->withoutOverlapping(10) // lock 10 mnt — query active-biz bisa 1-2 mnt
    ->runInBackground()
    ->name('dashboard-warm-admin');
