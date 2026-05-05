<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ProcessStoreChunkJob implements ShouldQueue
{
    use Queueable;
    
    private $deviceId;
    private $stores;
    private $schedulingPromotions;
    private $messageDelay;      // RENAME: delay -> messageDelay
    private $stopSending;
    private $restSending;
    private $baseTime;
    
    public function __construct($deviceId, $stores, $schedulingPromotions, $messageDelay, $stopSending, $restSending, $baseTime)
    {
        $this->deviceId = $deviceId;
        $this->stores = $stores;
        $this->schedulingPromotions = $schedulingPromotions;
        $this->messageDelay = $messageDelay;        // RENAME
        $this->stopSending = $stopSending;
        $this->restSending = $restSending;
        $this->baseTime = $baseTime;
    }
    
    public function handle()
    {
        $messageCount = 0;
        $totalRestTime = 0;
        
        foreach ($this->stores as $store) {
            if ($messageCount > 0 && $messageCount % $this->stopSending == 0) {
                $totalRestTime += $this->restSending;
            }
            
            $scheduleTime = $this->baseTime->copy()->addSeconds(($messageCount * $this->messageDelay) + $totalRestTime);
            
            // DEDUP by phone (not store_id) to prevent duplicates
            $message = BlashDetail::firstOrCreate([
                'blash_whatsapp_id' => $this->schedulingPromotions->id,
                'phone' => $store['phone'],
            ], [
                'store_id' => $store['id'],
                'status' => 'no',
                'sending_status' => 'no',
                'delivery_status' => 'queued',
                'schedule' => $scheduleTime->format('Y-m-d H:i:s'),
                'device_id' => $this->deviceId
            ]);

            // Skip if already existed (not newly created)
            if (!$message->wasRecentlyCreated) {
                continue;
            }
            
            // Dispatch dengan rate limiting
            dispatch(new SendPromotionWhatsappWithDelayJob($message))
                ->delay($scheduleTime)
                ->onQueue('whatsapp_sending');
                
            $messageCount++;
            
            // Jeda kecil untuk menghindari database overload
            usleep(10000); // 0.01 detik
        }
        
        Log::info("ProcessStoreChunkJob completed: Device {$this->deviceId}, {$messageCount} messages scheduled");
    }
}