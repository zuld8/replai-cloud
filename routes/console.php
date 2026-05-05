<?php

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
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendWhatsappJob)
    ->everyMinute()
    ->withoutOverlapping(30)
    ->when(function () {
        return BlashWhatsapp::where('status', 'pending')
            ->where("use", "whatsapp")
            ->where('schedule', '<=', now())
            ->exists();
    });

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