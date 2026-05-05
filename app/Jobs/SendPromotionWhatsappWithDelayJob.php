<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\ChatBot\HistoryChat;
use App\Models\Log;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Observers\WhatsappOfficial\WhatsappTemplateServiceObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log as FacadesLog;
use Exception;
use App\Supports\MimeTypes;

class SendPromotionWhatsappWithDelayJob implements ShouldQueue
{
    use Queueable;

    /**
     * Job timeout in seconds
     */
    public $timeout = 300;

    /**
     * Maximum number of attempts
     */
    public $tries = 3;

    /**
     * Store blast detail ID instead of model to prevent serialization issues
     */
    protected $blastId;

    /**
     * Constructor - Accept either ID or Model
     * 
     * @param int|BlashDetail $blast
     */
    public function __construct($blast)
    {
        if (is_object($blast) && $blast instanceof BlashDetail) {
            $this->blastId = $blast->id;
        } else {
            $this->blastId = $blast;
        }
    }

    /**
     * Execute the job
     * 
     * SIMPLIFIED VERSION: No cache, no reschedule - just execute!
     * Scheduling is already handled by SendWhatsappJob
     */
    public function handle(): void
    {
        try {
            // Safely retrieve the blast detail with error handling
            $blast = BlashDetail::find($this->blastId);

            if (!$blast) {
                FacadesLog::warning("BlashDetail not found: {$this->blastId}. Job skipped.");
                return;
            }

            // Skip if already processed
            if ($blast->status === 'yes') {
                FacadesLog::info("BlashDetail {$this->blastId} already processed. Skipping.");
                return;
            }

            // Just process the WhatsApp message - no reschedule logic!
            $this->processWhatsappMessage($blast);
        } catch (Exception $e) {
            FacadesLog::error("Error processing WhatsApp job {$this->blastId}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Process the WhatsApp message - SIMPLIFIED VERSION
     */
    private function processWhatsappMessage(BlashDetail $blast): void
    {
        // Get business settings
        $settings = Setting::where("id", ($blast->store->business_id ?? null))
            ->first(['whatsapp_sender', 'whatsapp_sender_notif', 'id']);

        // Create log entry
        $log = Log::create([
            'description' => __('blash.blash_description', [
                'name' => $blast->store->name ?? '',
                'schedule' => $blast->parent->name ?? ''
            ]),
            'merchant_id' => $blast->store->merchant_id ?? null,
            'business_id' => $blast->store->business_id ?? null,
            'type' => 'whatsapp',
            'device_id' => $blast->device_id
        ]);

        if (!$settings) {
            $this->logError($log, 'Tidak dapat mendeteksi pengaturan');
            $blast->update(['status' => 'yes', 'reports' => 'Tidak dapat mendeteksi pengaturan']);
            return;
        }

        // Validate business limits
        if (!$this->validateBusinessLimits($blast, $settings, $log)) {
            return;
        }

        // Increment WhatsApp sender counter
        $settings->increment('whatsapp_sender');

        // Process and send the message directly - NO RESCHEDULE!
        $this->sendWhatsappMessage($blast, $settings, $log);
    }

    /**
     * Validate business limits
     */
    private function validateBusinessLimits(BlashDetail $blast, Setting $settings, Log $log): bool
    {
        if (!$blast->store->business_id) {
            return true;
        }

        $merchant = $blast->store->merchant ?? null;
        if (!$settings || !$merchant) {
            return true;
        }

        $transaction = $merchant->package_active;
        if (!$transaction) {
            $blast->update([
                'status' => 'yes',
                'reports' => 'Paket Langganan telah Berakhir'
            ]);
            $this->logError($log, 'Paket Langganan telah Berakhir');
            return false;
        }

        if ($transaction->limit_whatsapp_option == 'yes') {
            if ($settings->whatsapp_sender >= $transaction->whatsapp_limit) {
                $blast->update([
                    'status' => 'yes',
                    'reports' => 'Limit Pengiriman Harian Telah Habis'
                ]);
                $this->logError($log, 'Limit Pengiriman harian telah habis');
                return false;
            }
        }

        return true;
    }

    /**
     * Send WhatsApp message
     */
    private function sendWhatsappMessage(BlashDetail $blast, Setting $settings, Log $log): void
    {
        // Initialize observers
        $whatsappNotificationObserver = new WhatsappOfficialServiceObserver();
        $whatsappServiceObserver = new WhatsappServiceObserver();
        $whatsappTemplateObserver = new WhatsappTemplateServiceObserver();

        // Prepare message variables
        $storeName = $blast->store->name ?? '';
        $categoryName = $blast->store->category->name ?? '';
        $phone = $blast->phone ?? '';
        $email = $blast->email ?? '';
        $address = $blast->store->address ?? '';
        $message = $blast->parent->template->message ?? null;
        $wabaOfficial = $blast->parent->waba ?? 'no';

        if (!$message) {
            $this->logError($log, 'Template message tidak ditemukan');
            $blast->update(['status' => 'yes', 'reports' => 'Template message tidak ditemukan']);
            return;
        }

        // Replace template variables
        $message = str_replace(
            ['{name}', '{category}', '{phone}', '{email}', '{address}'],
            [$storeName, $categoryName, $phone, $email, $address],
            $message
        );

        // Get available device
        $account = $this->getDevice($blast, $wabaOfficial, $settings);

        if (!$account) {
            $this->logError($log, 'Device tidak tersedia');
            $blast->update(['status' => 'yes', 'reports' => 'Device tidak tersedia']);
            return;
        }

        // Prepare message variables
        $messageVariable = $this->prepareMessageVariable($blast, $message, $account, $wabaOfficial);

        if (!$messageVariable) {
            $this->logError($log, 'Gagal menyiapkan variabel pesan');
            $blast->update(['status' => 'yes', 'reports' => 'Gagal menyiapkan variabel pesan']);
            return;
        }

        // Validate credit
        if (!$this->validateCredit($blast)) {
            $messageVariable['message'] = 'Credit Token Anda tidak mencukupi';
            $this->handleNoCredit($blast, $messageVariable, $log);
            return;
        }

        // Send message or handle no phone
        if (!empty($phone)) {
            $this->executeMessageSending(
                $blast,
                $wabaOfficial,
                $account,
                $messageVariable,
                $whatsappNotificationObserver,
                $whatsappServiceObserver,
                $whatsappTemplateObserver,
                $log
            );
        } else {
            $this->handleNoPhone($blast, $messageVariable, $log);
        }
    }

    /**
     * Get device for sending message
     */
    private function getDevice(BlashDetail $blast, string $wabaOfficial, Setting $settings)
    {
        // If specific device_id is assigned, try to get that device first
        if ($blast->device_id) {
            $device = $this->getSpecificDevice($blast->device_id, $wabaOfficial, $settings);
            if ($device) {
                return $device;
            }

            // If assigned device not available, log warning but continue with fallback
            FacadesLog::warning("Assigned device {$blast->device_id} not available for blast {$blast->id}, using fallback");
        }

        // Fallback to available device selection
        return $this->getAvailableDevice($blast, $wabaOfficial, $settings);
    }

    /**
     * Get specific device by ID
     */
    private function getSpecificDevice(string $deviceId, string $wabaOfficial, Setting $settings)
    {
        if ($wabaOfficial == 'yes') {
            return WhatsappKeyAccount::where('id', $deviceId)
                ->where("status", "active")
                ->first();
        } else {
            return WhatsappDevice::where('id', $deviceId)
                ->where("business_id", $settings->id)
                ->where("status", "active")
                ->first();
        }
    }

    /**
     * Get available device using selection method
     */
    private function getAvailableDevice(BlashDetail $blast, string $wabaOfficial, Setting $settings)
    {
        $query = $wabaOfficial == 'yes' ?
            WhatsappKeyAccount::where(function ($q) {
                return $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no");
            })->where("status", "active") :
            WhatsappDevice::where(function ($q) {
                return $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no");
            })->where("business_id", $settings->id)->where("status", "active");

        // Filter by selected devices if specified
        if ($blast->parent->devices != null) {
            $deviceIds = explode(',', $blast->parent->devices);
            $query->whereIn('id', $deviceIds);
        }

        $whatsappAccounts = $query->get();

        if ($whatsappAccounts->isEmpty()) {
            return null;
        }

        // Apply selection method
        $method = $blast->parent->whatsapp_sender_notif ?? 'sequence';

        switch ($method) {
            case 'sequence':
                return $whatsappAccounts->first();
            case 'spin':
                $totalSend = $blast->parent->sender->count();
                $index = $totalSend % $whatsappAccounts->count();
                return $whatsappAccounts->get($index);
            case 'random':
                return $whatsappAccounts->random();
            default:
                return $whatsappAccounts->first();
        }
    }

    /**
     * Prepare message variables for different WhatsApp types
     */
    private function prepareMessageVariable(BlashDetail $blast, string $message, $account, string $wabaOfficial): ?array
    {
        if ($wabaOfficial == 'yes') {
            $config = $account->meta_data;
            $config = $config ? json_decode($config, true) : [];

            return [
                'appid' => $account->meta->app_id ?? null,
                'phoneid' => $config['whatsapp']['phone_number_id'] ?? null,
                'wabaid' => $account->meta->business_app ?? null,
                'access_token' => $account->meta->access_token ?? null,
            ];
        } else {
            return [
                'message' => urldecode($message),
                'template_body' => array(),
                'whatsapp_key' => $account->id,
                'whatsapp_session' => $account->id,
                'file' => $blast->parent->template->image != null ?
                    asset($blast->parent->template->image) : '',
                'phone' => $blast->store->phone ?? null,
                'type' => $blast->parent->template->type_content ?? 'description',
                'datas' => json_decode($blast->parent->template->button_or_list ?? '[]', true)
            ];
        }
    }

    /**
     * Validate credit availability
     */
    private function validateCredit(BlashDetail $blast): bool
    {
        $business = $blast->parent->business;
        if (!$business || !$business->merchant) {
            return true; // Skip validation if no merchant
        }

        $topupLimit = $business->package_active_topup->sisa_credit ?? 0;
        $packageCredit = $business->package_active->sisa_credit ?? 0;
        $totalLimit = ($topupLimit + $packageCredit);
        $usageCredit = 1;

        if ($totalLimit < $usageCredit) {
            return false;
        }

        // Deduct credit
        if ($packageCredit > 0) {
            $business->package_active->increment('using_credit_limit', $usageCredit);
        } else {
            $business->package_active_topup->increment('using_credit_limit', $usageCredit);
        }

        return true;
    }

    /**
     * Execute message sending
     */
    private function executeMessageSending(
        BlashDetail $blast,
        string $wabaOfficial,
        $account,
        array $messageVariable,
        WhatsappOfficialServiceObserver $whatsappNotificationObserver,
        WhatsappServiceObserver $whatsappServiceObserver,
        WhatsappTemplateServiceObserver $whatsappTemplateObserver,
        Log $log
    ): void {
        try {
            if ($wabaOfficial == 'yes') {
                $templateBuilder = $whatsappTemplateObserver->buildTemplate($blast->parent);
                $result = $whatsappNotificationObserver->sendTemplateMessage($blast->store, $templateBuilder, $messageVariable);
                $this->handleResult($blast, $wabaOfficial, $result, $account, $log);
            } else {
                $result = $whatsappServiceObserver->sendMessage(
                    $blast->phone,
                    $messageVariable['whatsapp_key'],
                    $messageVariable['message'],
                    $messageVariable['file'],
                    $messageVariable['type'],
                    $messageVariable['datas']
                );
                $this->handleResult($blast, $wabaOfficial, $result, $account, $log, $messageVariable['message']);
            }

            // Update device daily send counter
            $account->increment('daily_send');
        } catch (Exception $e) {
            $this->handleResult($blast, $wabaOfficial, [
                'status' => 500,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], $account, $log);
        }
    }

    /**
     * Handle sending result
     */
    private function handleResult(BlashDetail $blast, string $wabaOfficial, array $result, $account, Log $log, ?string $message = null): void
    {
        $isSuccess = ($result['status'] ?? 500) == 200;

        $updateData = [
            'status' => 'yes',
            'sending' => now()->format('Y-m-d H:i:s'),
            'phone' => $blast->store->phone ?? '',
            'device_id' => $account->id,
        ];

        $logData = [
            'status' => $isSuccess ? 'success' : 'error',
            'store_id' => $blast->store_id,
            'sending' => now()->format('Y-m-d H:i:s'),
            'device_id' => $account->id,
        ];

        if ($message) {
            $updateData['text'] = $message;
            $logData['text'] = $message;
        }

        if ($isSuccess) {
            $updateData['reports'] = null;
            $updateData['wamid'] = $result['messageid'] ?? null;
            $updateData['delivery_status'] = 'sent';
            $updateData['sending_status'] = 'yes';

            if ($wabaOfficial == 'yes') {
                $history    = HistoryChat::where("whatsapp_waba_id", $blast->device_id)->where("from_number", $blast->phone)->first();

                if (!$history) {
                    $history    = HistoryChat::create([
                        'whatsapp_waba_id'  => $blast->device_id,
                        'name'              => $blast->store->name ?? '-',
                        'merchant_id'       => $blast->parent->merchant_id ?? null,
                        'type'              => 'personal',
                        'from_number'       => $blast->phone,
                        'business_id'       => $blast->parent->business_id ?? null,
                        'from'              => 'waba',
                        'takeover'          => 'no',
                        'status'            => 'open',
                        'label'             => null,
                    ]);
                }
            } else {
                $history    = HistoryChat::where("whatsapp_waba_id", $blast->device_id)->where("from", $blast->phone)->first();

                if (!$history) {
                    $history    = HistoryChat::create([
                        'device_id'         => $blast->device_id,
                        'name'              => $blast->store->name ?? '-',
                        'merchant_id'       => $blast->parent->merchant_id ?? null,
                        'type'              => 'personal',
                        'from_number'       => $blast->phone,
                        'business_id'       => $blast->parent->business_id ?? null,
                        'from'              => 'whatsapp',
                        'takeover'          => 'no',
                        'status'            => 'open',
                        'label'             => null,
                    ]);
                }
            }

            $template   = $blast->parent->template ?? null;

            if ($history && $template) {

                $text   = '';
                $image  = array(
                    'path'      => null,
                    'type'      => null,
                    'size'      => null,
                    'status'    => false,
                    'message_type'  => 'text'
                );

                if ($wabaOfficial != 'yes') {
                    $text   = $template->message ?? '-';
                    $image  = $template->image != null ? $this->analyzeImage($template->image) : $image;
                } else {

                    $templateDetails    = json_decode($blast->parent->metadata, true);

                    if ($templateDetails['body'] && isset($templateDetails['body']['text'])) {
                        $text = $templateDetails['body']['text'];
                    }

                    $image              = $blast->parent->file != null ? $this->analyzeImage($blast->parent->file) : $image;
                }

                $history->details()->create([
                    'file_path' => $image['path'],
                    'file_type' => $image['type'],
                    'file_size' => $image['size'],
                    'type' => $image['message_type'],
                    'history_chat_id' => $history->id,
                    'is_read'           => 'yes',
                    'from' => 'device',
                    'message' => $text,
                ]);
            }
        } else {
            $updateData['sending_status'] = 'no';
            $updateData['delivery_status'] = 'failed';
            $updateData['reports'] = $result['message'] ?? 'Unknown error';
            $logData['error'] = $result['message'] ?? 'Unknown error';
        }

        // Update via DB::table to bypass $fillable restrictions for delivery tracking
        \DB::table('blash_details')->where('id', $blast->id)->update($updateData);
        $log->update($logData);

        FacadesLog::info("WhatsApp message " . ($isSuccess ? 'sent successfully' : 'failed') .
            " for blast {$blast->id} via device {$account->id}" .
            ($isSuccess && isset($updateData['wamid']) ? " [wamid: {$updateData['wamid']}]" : ""));
    }

    function buildBodyComponent($metadata)
    {
        $bodyComponent = [
            'type' => 'body',
            'parameters' => [],
        ];

        if ($metadata['body']['parameters']) {
            foreach ($metadata['body']['parameters'] as $parameter) {
                $param['type'] = $parameter['type'];
                $param['text'] = $parameter['value'];

                $bodyComponent['parameters'][] = $param;
            }
        }

        return $bodyComponent;
    }

    private function analyzeImage($image): array
    {
        $file_data = [
            'path' => null,
            'type' => null,
            'size' => null,
            'status' => false,
            'message_type' => 'text'
        ];

        // Perbaikan 2: Konsisten dalam penggunaan path
        $file_path = public_path($image);

        if (!file_exists($file_path)) {
            return $file_data;
        }

        $file_type = mime_content_type($file_path);
        $file_size = filesize($file_path);

        // Perbaikan 3: Inisialisasi variable dengan default value
        $message_type = 'text'; // Default value

        if ($file_type) {
            foreach (MimeTypes::TYPE_MAP as $key => $mime_types) {
                if (in_array($file_type, $mime_types)) {
                    $message_type = $key;
                    break;
                }
            }
        }

        $file_data = [
            'type' => $file_type,
            'size' => $file_size,
            'path' => $image,
            'status' => true,
            'message_type' => $message_type
        ];

        return $file_data;
    }


    /**
     * Handle case when no phone number available
     */
    private function handleNoPhone(BlashDetail $blast, array $messageVariable, Log $log): void
    {
        $blast->update([
            'status' => 'yes',
            'sending' => now()->format('Y-m-d H:i:s'),
            'phone' => $blast->store->phone ?? '',
            'text' => $messageVariable['message'] ?? '',
            'sending_status' => 'no',
            'device_id' => $messageVariable['whatsapp_session'] ?? null,
            'reports' => __('blash.phone_nothing')
        ]);

        $log->update([
            'status' => 'error',
            'store_id' => $blast->store_id,
            'sending' => now()->format('Y-m-d H:i:s'),
            'text' => $messageVariable['message'] ?? '',
            'device_id' => $messageVariable['whatsapp_session'] ?? null,
            'error' => __('blash.wa_not_registered')
        ]);
    }

    /**
     * Handle case when credit is insufficient
     */
    private function handleNoCredit(BlashDetail $blast, array $messageVariable, Log $log): void
    {
        $blast->update([
            'status' => 'yes',
            'sending' => now()->format('Y-m-d H:i:s'),
            'phone' => $blast->store->phone ?? '',
            'text' => $messageVariable['message'] ?? '',
            'sending_status' => 'no',
            'device_id' => $messageVariable['whatsapp_session'] ?? null,
            'reports' => 'Credit Token Anda tidak mencukupi'
        ]);

        $log->update([
            'status' => 'error',
            'store_id' => $blast->store_id,
            'sending' => now()->format('Y-m-d H:i:s'),
            'text' => $messageVariable['message'] ?? '',
            'device_id' => $messageVariable['whatsapp_session'] ?? null,
            'error' => 'Credit Token Anda tidak mencukupi'
        ]);
    }

    /**
     * Helper method to log errors
     */
    private function logError(Log $log, string $error): void
    {
        $log->update([
            'status' => 'error',
            'error' => $error
        ]);
        FacadesLog::error("WhatsApp job error: {$error} - Blast ID: {$this->blastId}");
    }

    /**
     * Handle failed job
     */
    public function failed(Exception $exception): void
    {
        FacadesLog::error("WhatsApp job failed permanently: {$exception->getMessage()} - Blast ID: {$this->blastId}");

        // Try to update blast status if possible
        $blast = BlashDetail::find($this->blastId);
        if ($blast) {
            $blast->update([
                'status' => 'yes',
                'sending_status' => 'no',
                'reports' => 'Job failed: ' . $exception->getMessage()
            ]);
        }
    }
}
