<?php

namespace App\Jobs;

use App\Models\WhatsappDevice;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NonActiveDeviceExpiredJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $whatsappServiceObserver = new WhatsappServiceObserver();
        $devices = WhatsappDevice::whereHas('business', function ($q) {
            $q->whereDoesntHave('package_active')->where('merchant_id', '!=', null);
        })->limit(50)->get();

        $deactivated = 0;
        $deleted = 0;
        $errors = 0;

        foreach ($devices as $device) {
            try {
                $device->update([
                    'status' => 'no_active'
                ]);
                $deactivated++;

                // Try to check & delete session, but don't crash if WA server is down
                try {
                    $response = $whatsappServiceObserver->checkSession($device);
                    if ($response->status() == 200) {
                        $whatsappServiceObserver->deleteSession($device);
                        $deleted++;
                    }
                } catch (\Exception $e) {
                    // WA server might be unreachable — that's OK, device is already deactivated
                    Log::warning("NonActiveDeviceExpiredJob: Could not reach WA server for device {$device->id}: {$e->getMessage()}");
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("NonActiveDeviceExpiredJob: Error processing device {$device->id}: {$e->getMessage()}");
            }
        }

        Log::info("NonActiveDeviceExpiredJob completed: {$deactivated} deactivated, {$deleted} sessions deleted, {$errors} errors");
    }
}
