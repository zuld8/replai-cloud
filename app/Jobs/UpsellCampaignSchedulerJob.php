<?php

// File: app/Jobs/UpsellCampaignSchedulerJob.php
namespace App\Jobs;

use App\Models\Blash\BlashWhatsapp;
use App\Models\Store\Store;
use App\Services\Sistem\QueueRouter;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpsellCampaignSchedulerJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $now = now();

        // Get campaigns that should run now
        $campaigns = BlashWhatsapp::where('use', 'whatsapp_follow_up')
            ->where('status', 'pending')
            // ->where('time', date('H:i'))
            ->where(function ($query) use ($now) {
                $query->where('schedule_frequency', 'once')
                    ->where('start_date', '<=', $now->format('Y-m-d'))
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $now->format('Y-m-d'));
                    });
            })
            ->orWhere(function ($query) use ($now) {
                $query->where('use', 'whatsapp_follow_up')
                    ->where('status', 'pending')
                    ->where('schedule_frequency', 'daily')
                    ->where('start_date', '<=', $now->format('Y-m-d'))
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $now->format('Y-m-d'));
                    })
                    ->where(function ($q) use ($now) {
                        $dayName = strtolower($now->format('l'));
                        $q->whereRaw("FIND_IN_SET(?, days) > 0", [$dayName]);
                    });
            })
            ->orWhere(function ($query) use ($now) {
                $query->where('use', 'whatsapp_follow_up')
                    ->where('status', 'pending')
                    ->where('schedule_frequency', 'monthly')
                    ->where('start_date', '<=', $now->format('Y-m-d'))
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $now->format('Y-m-d'));
                    })
                    ->where('schedule', $now->day);
            })
            ->orWhere(function ($query) use ($now) {
                $query->where('use', 'whatsapp_follow_up')
                    ->where('status', 'pending')
                    ->where('schedule_frequency', 'yearly')
                    ->where('start_date', '<=', $now->format('Y-m-d'))
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $now->format('Y-m-d'));
                    })
                    ->where('month', $now->month)
                    ->where('yearly', $now->day);
            })
            ->limit(100)->get();

        foreach ($campaigns as $campaign) {
            if ($this->shouldRunCampaignNow($campaign, $now)) {
                $queue = QueueRouter::getQueue($campaign->business_id, 'upsell');
                dispatch(new UpsellCampaignGeneratorJob($campaign))
                    ->onQueue($queue);
            }
        }
    }

    private function shouldRunCampaignNow(BlashWhatsapp $campaign, Carbon $now): bool
    {

        // For 'once' campaigns - always check for new contacts, don't use cache
        if ($campaign->schedule_frequency === 'once') {
            // Check if there are any eligible contacts (those not yet sent to)
            $hasEligibleContacts = Store::whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where('phone', '!=', '0')
                ->whereNotExists(function ($subQuery) use ($campaign) {
                    $subQuery->select('id')
                        ->from('blash_details')
                        ->whereColumn('blash_details.store_id', 'stores.id')
                        ->where('blash_details.blash_whatsapp_id', $campaign->id);
                })
                ->limit(1)
                ->exists();

            return $hasEligibleContacts;
        }

        // For recurring campaigns (daily, monthly, yearly) - check if already ran today
        $lastRun = cache()->get("upsell_campaign_last_run_{$campaign->id}");
        if ($lastRun && Carbon::parse($lastRun)->isToday()) {
            return false;
        }

        return true;
    }
}
