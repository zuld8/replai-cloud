<?php
// File: app/Jobs/SendUpsellCampaignJob.php
namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Services\Sistem\QueueRouter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendUpsellCampaignJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Get pending upsell campaign messages that are scheduled to be sent
        $blastDetails = BlashDetail::whereHas('parent', function ($query) {
            $query->where('use', 'whatsapp_follow_up');
        })
            ->where('status', 'no')
            ->where('schedule', '<=', now())
            ->orderBy('schedule')
            ->limit(100) // Process in batches
            ->get();


        Log::info(count($blastDetails) . ' - info data');
        if ($blastDetails->isEmpty()) {
            return;
        }

        foreach ($blastDetails as $blastDetail) {
            try {

                $queue = QueueRouter::getQueue(($blastDetail->parent->business_id ?? '-'), 'upsell');
                dispatch(new SendUpsellMessageJob($blastDetail))
                    ->onQueue($queue)
                    ->delay(now()->addSeconds(1));
            } catch (\Exception $e) {

                $blastDetail->update([
                    'status' => 'yes',
                    'sending_status' => 'yes',
                    'reports' => 'Failed to dispatch: ' . $e->getMessage()
                ]);
            }
        }
    }
}
