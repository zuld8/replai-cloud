<?php

// File: app/Jobs/SendUpsellMessageJob.php
namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Log;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class SendUpsellMessageJob implements ShouldQueue
{
    use Queueable;

    protected $blastDetail;

    public function __construct(BlashDetail $blastDetail)
    {
        $this->blastDetail = $blastDetail;
    }

    public function handle(): void
    {
        // Skip if already processed
        if ($this->blastDetail->status === 'yes') {
            return;
        }

        $campaign = $this->blastDetail->parent;
        if (!$campaign) {
            $this->blastDetail->update([
                'status' => 'yes', 
                'reports' => 'Campaign not found'
            ]);
            return;
        }

        $store = $this->blastDetail->store;
        if (!$store) {
            $this->blastDetail->update([
                'status' => 'yes', 
                'reports' => 'Store not found'
            ]);
            return;
        }

        // Get business settings
        $settings = Setting::where("id", $store->business_id)->first();
        if (!$settings) {
            $this->blastDetail->update([
                'status' => 'yes', 
                'reports' => 'Business settings not found'
            ]);
            return;
        }

        // Create log entry
        $log = Log::create([
            'description' => "Upsell Campaign: {$campaign->name} to {$store->name}",
            'merchant_id' => $store->merchant_id,
            'business_id' => $store->business_id,
            'type' => 'whatsapp',
            'device_id' => $this->blastDetail->device_id
        ]);

        // Validate business limits
        if (!$this->validateBusinessLimits($settings, $log, $store)) {
            return;
        }

        // Check device availability and rate limiting
        if (!$this->validateDevice($log)) {
            return;
        }

        // Get device
        $device = WhatsappDevice::where('id', $this->blastDetail->device_id)
            ->where('business_id', $settings->id)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            $this->blastDetail->update([
                'status' => 'yes', 
                'reports' => 'Device not available'
            ]);
            $log->update([
                'status' => 'error',
                'error' => 'Device not available'
            ]);
            return;
        }

        // Prepare message
        $message = $this->prepareMessage($campaign, $store);
        if (!$message) {
            $this->blastDetail->update([
                'status' => 'yes', 
                'reports' => 'Failed to prepare message'
            ]);
            return;
        }

        // Update settings counter
        $settings->increment('whatsapp_sender');

        // Track device usage
        $this->trackDeviceUsage($device->id);

        // Send message
        $this->sendMessage($device, $message, $log);
    }

    private function validateBusinessLimits($settings, $log, $store): bool
    {
        if ($store->business_id != null) {
            $merchant = $store->merchant;
            if ($settings && $merchant) {
                $transaction = $merchant->package_active;
                if (!$transaction) {
                    $this->blastDetail->update([
                        'status' => 'yes',
                        'sending_status' => 'yes',
                        'reports' => 'Paket Langganan telah Berakhir'
                    ]);
                    $log->update([
                        'status' => 'error',
                        'error' => 'Paket Langganan telah Berakhir'
                    ]);
                    return false;
                }

                if ($transaction->limit_whatsapp_option == 'yes') {
                    if ($settings->whatsapp_sender >= $transaction->whatsapp_limit) {
                        $this->blastDetail->update([
                            'status' => 'yes',
                            'sending_status' => 'yes',
                            'reports' => 'Limit Pengiriman Harian Telah Habis'
                        ]);
                        $log->update([
                            'status' => 'error',
                            'error' => 'Limit Pengiriman harian telah habis'
                        ]);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function validateDevice($log): bool
    {
        $deviceId = $this->blastDetail->device_id;
        $campaign = $this->blastDetail->parent;
        $stopSending = $campaign->stop_sending ?? 20;

        // Check device rate limiting
        $currentBatch = Cache::get("device_current_batch_{$deviceId}", 0);

        if ($currentBatch >= $stopSending) {
            $lastRest = Cache::get("device_last_rest_{$deviceId}");
            $restSending = $campaign->rest_sending ?? 90;

            if (!$lastRest || now()->diffInSeconds($lastRest) >= $restSending) {
                // Reset batch counter
                Cache::put("device_current_batch_{$deviceId}", 0, now()->addDay());
                Cache::put("device_last_rest_{$deviceId}", now(), now()->addDay());
                return true;
            } else {
                // Reschedule for later
                $waitTime = $restSending - now()->diffInSeconds($lastRest);
                dispatch(new SendUpsellMessageJob($this->blastDetail))
                    ->delay(now()->addSeconds($waitTime + 5));

                $log->update([
                    'status' => 'rescheduled',
                    'error' => "Device resting, rescheduled for {$waitTime} seconds"
                ]);
                return false;
            }
        }

        return true;
    }

    private function trackDeviceUsage($deviceId): void
    {
        $currentBatch = Cache::get("device_current_batch_{$deviceId}", 0);
        $sentToday = Cache::get("device_sent_today_{$deviceId}", 0);

        Cache::put("device_current_batch_{$deviceId}", $currentBatch + 1, now()->addDay());
        Cache::put("device_sent_today_{$deviceId}", $sentToday + 1, now()->addDay());
    }

    private function prepareMessage($campaign, $store): ?string
    {
        if ($campaign->broadcast_method === 'ai') {
            // For AI method, use the prompt to generate message
            // This would need integration with your AI service
            return $this->generateAIMessage($campaign->ai_prompt, $store);
        } else {
            // For template method
            $template = $campaign->template;
            if (!$template) {
                return null;
            }

            $message = $template->message;

            // Replace placeholders
            $replacements = [
                '{name}' => $store->name ?? '',
                '{category}' => $store->category->name ?? '',
                '{phone}' => $store->phone ?? '',
                '{email}' => $store->email ?? '',
                '{address}' => $store->address ?? '',
                '{owner}' => $store->pemilik ?? '',
            ];

            return str_replace(array_keys($replacements), array_values($replacements), $message);
        }
    }

    private function generateAIMessage($prompt, $store): string
    {
        // Placeholder for AI message generation
        // You would integrate this with your AI service
        $context = [
            'store_name' => $store->name,
            'category' => $store->category->name ?? '',
            'owner' => $store->pemilik ?? '',
        ];

        // For now, return a simple template
        return "Halo {$store->name}, " . $prompt;
    }

    private function sendMessage($device, $message, $log): void
    {
        $whatsappService = new WhatsappServiceObserver();

        $messageData = [
            'message' => urldecode($message),
            'template_body' => [],
            'whatsapp_key' => $device->id,
            'whatsapp_session' => $device->id,
            'file' => '',
            'phone' => $this->blastDetail->store->phone,
            'type' => 'description',
            'datas' => []
        ];

        try {
            $result = $whatsappService->sendMessage(
                $this->blastDetail->phone,
                $messageData['whatsapp_key'],
                $messageData['message'],
                $messageData['file'],
                $messageData['type'],
                $messageData['datas']
            );

            $this->handleResult($result, $device, $log, $message);
        } catch (\Exception $e) {
            $this->blastDetail->update([
                'status' => 'yes',
                'sending' => now(),
                'sending_status' => 'yes',
                'reports' => 'Exception: ' . $e->getMessage(),
                'text' => $message,
                'device_id' => $device->id,
            ]);

            $log->update([
                'status' => 'error',
                'error' => 'Exception: ' . $e->getMessage(),
                'text' => $message,
                'device_id' => $device->id,
            ]);
        }
    }

    private function handleResult($result, $device, $log, $message): void
    {
        $updateData = [
            'status' => 'yes',
            'sending' => now(),
            'phone' => $this->blastDetail->store->phone ?? '',
            'device_id' => $device->id,
            'text' => $message,
        ];

        $logData = [
            'status' => $result['status'] == 200 ? 'success' : 'error',
            'store_id' => $this->blastDetail->store_id,
            'sending' => now(),
            'device_id' => $device->id,
            'text' => $message,
        ];

        if ($result['status'] == 200) {
            $updateData['sending_status'] = 'yes';
            $updateData['reports'] = 'Message sent successfully';
        } else {
            $updateData['sending_status'] = 'yes';
            $updateData['reports'] = $result['message'] ?? 'Unknown error';
            $logData['error'] = $result['message'] ?? 'Unknown error';
        }

        $this->blastDetail->update($updateData);
        $log->update($logData);

        // Update device daily send counter
        $device->increment('daily_send');
    }
}
