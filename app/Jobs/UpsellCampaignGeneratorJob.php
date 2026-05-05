<?php

// File: app/Jobs/UpsellCampaignGeneratorJob.php
namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Store\Store;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpsellCampaignGeneratorJob implements ShouldQueue
{
    use Queueable;

    protected $campaign;

    public function __construct(BlashWhatsapp $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        Log::info("Generating details for upsell campaign: {$this->campaign->name}");

        // Get devices for this campaign
        $deviceIds = !empty($this->campaign->devices) ? explode(',', $this->campaign->devices) : [];

        if (empty($deviceIds)) {
            Log::error("No devices configured for campaign: {$this->campaign->name}");
            return;
        }

        // Process target contacts (ini yang melakukan semua processing)
        $totalProcessed = $this->processTargetContacts();

        Log::info($totalProcessed . ' - Kontak');
        if ($totalProcessed === 0) {
            Log::info("No target contacts found for campaign: {$this->campaign->name}");
            return;
        }

        // Mark campaign as processed for today
        if ($this->campaign->schedule_frequency !== 'once') {
            cache()->put("upsell_campaign_last_run_{$this->campaign->id}", now(), now()->addDay());
        }

        Log::info("Generated {$totalProcessed} blast details for campaign: {$this->campaign->name}");
    }

    private function buildTargetContactsQuery()
    {
        $query = Store::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where('phone', '!=', '0');

        // Filter by category if specified
        if ($this->campaign->category_id) {
            $query->where('category_id', $this->campaign->category_id);
        }

        // Filter by labels if specified
        if (!empty($this->campaign->labels)) {
            $labelIds = explode(',', $this->campaign->labels);
            $query->whereHas('history', function ($q) use ($labelIds) {
                $q->whereIn('label', $labelIds);
            });
        }

        // Filter by history chat conditions
        $query->where(function ($q) {
            // If store has history chat, check conditions
            $q->whereHas('history', function ($historyQuery) {
                $historyQuery->where('status', '!=', 'blocked')
                    ->whereIn('from', ['whatsapp', 'waba']);
            })
                // OR if store doesn't have history chat, just use store data
                ->orWhereDoesntHave('history');
        });

        if ($this->campaign->schedule_frequency === 'once') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select('id')
                    ->from('blash_details')
                    ->whereColumn('blash_details.store_id', 'stores.id')
                    ->where('blash_details.blash_whatsapp_id', $this->campaign->id);
            });
        }

        return $query->with('history');
    }

    private function processTargetContacts(): int
    {
        $query = $this->buildTargetContactsQuery();

        $totalProcessed = 0;
        $delay = $this->campaign->delay ?? 60;
        $deviceIds = !empty($this->campaign->devices) ? explode(',', $this->campaign->devices) : [];

        // Process in chunks of 1000 records
        $query->chunk(1000, function ($contacts) use (&$totalProcessed, $delay, $deviceIds) {
            $blastDetails = [];
            $currentTime = now();

            foreach ($contacts as $index => $contact) {
                $globalIndex = $totalProcessed + $index;
                $scheduleTime = $currentTime->copy()->addSeconds($delay * $globalIndex);
                $deviceId = $this->selectDevice($deviceIds, $globalIndex);

                $blastDetails[] = [
                    'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                    'blash_whatsapp_id' => $this->campaign->id,
                    'phone' => $contact->phone,
                    'email' => $contact->email,
                    'store_id' => $contact->id,
                    'status' => 'no', 
                    'schedule' => $scheduleTime,
                    'type' => $contact->history ? $contact->history->from : 'whatsapp',
                    'device_id' => $deviceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert for better performance
            BlashDetail::insert($blastDetails);
            $totalProcessed += count($contacts);
        });

        return $totalProcessed;
    }


    private function selectDevice(array $deviceIds, int $index): String
    {
        $method = $this->campaign->whatsapp_sender_notif ?? 'spin';

        switch ($method) {
            case 'sequence':
                return $deviceIds[0];

            case 'spin':
                return $deviceIds[$index % count($deviceIds)];

            case 'random':
                return $deviceIds[array_rand($deviceIds)];

            default:
                return $deviceIds[0];
        }
    }
}
