<?php

namespace App\Services\Platform\Telegram;

use App\Models\ChatBot\ChatBot;
use App\Models\Courier\CourierFineTunnel;
use App\Models\InternalSetting;
use Illuminate\Support\Facades\Log;

class TelegramHandlerService
{
    private $telegramService;
    private $openAiService;
    private $rajaOngkirObserver;
    private $biteshipService;
    private $generalSetting;

    public function __construct(
        $telegramService,
        $openAiService,
        $rajaOngkirObserver,
        $biteshipService,
        $generalSetting
    ) {
        $this->openAiService = $openAiService;
        $this->rajaOngkirObserver = $rajaOngkirObserver;
        $this->biteshipService = $biteshipService;
        $this->generalSetting = $generalSetting;
    }

    public function processResponse($histories, TelegramProcessorService $processor, $settings): array
    {
        $responses = [];
        $device = $processor->getDevice();
        $fineTunnel = $device->finetunnel;
        $message = $processor->getMessage();

        // Process welcome message if first interaction
        if ($fineTunnel && $fineTunnel->welcome_message && $histories->details->count() == 1) {
            $welcomeResponse = $this->createWelcomeMessage($fineTunnel, $histories);
            if ($welcomeResponse) {
                $responses = $welcomeResponse;
            }
        }

        // Process AI responses
        if ($this->shouldProcessAiResponse($device, $histories, $settings, $fineTunnel)) {
            $aiResponse = $this->processAiResponse($histories, $processor, $fineTunnel, $settings);
            if ($aiResponse) {
                $responses = $aiResponse;
            }
        }

        // Process chatbot responses
        if ($this->shouldProcessChatbot($device, $histories)) {
            $chatbotResponse = $this->processChatbotResponse($device, $message, $histories);
            if ($chatbotResponse) {
                $responses = $chatbotResponse;
            }
        }

        return $responses;
    }

    private function createWelcomeMessage($fineTunnel, $histories): ?array
    {
        $messageType = 'text';
        $filePath = null;
        $fileType = null;
        $fileSize = null;

        if ($fineTunnel->welcome_image && file_exists(public_path($fineTunnel->welcome_image))) {
            $filePath = $fineTunnel->welcome_image;
            $fileType = mime_content_type(public_path($filePath));
            $fileSize = filesize(public_path($filePath));
            $messageType = $this->determineMessageTypeFromMime($fileType);
        }

        $record = $histories->details()->create([
            'message' => $fineTunnel->welcome_message ?? '',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'type' => $messageType,
            'history_chat_id' => $histories->id,
            'from' => 'device'
        ]);

        return [
            'message' => $fineTunnel->welcome_message ?? '',
            'file_path' => $filePath ? asset($filePath) : null,
            'from'      => 'welcome',
            'record' => $record
        ];
    }

    private function shouldProcessAiResponse($device, $histories, $settings, $fineTunnel): bool
    {
        if (!in_array($device->auto_reply_method, ['ai', 'all'])) {
            return false;
        }

        if ($histories->takeover != 'no' || $histories->status == 'block') {
            return false;
        }

        if ($settings->is_online != 'yes' || !$fineTunnel) {
            return false;
        }

        // Check credit availability
        if ($device->business->merchant ?? null) {
            $topupLimit = $device->business->package_active_topup->sisa_credit ?? 0;
            $packageCredit = $device->business->package_active->sisa_credit ?? 0;
            return $topupLimit > 0 || $packageCredit > 0;
        }

        return true;
    }

    private function shouldProcessChatbot($device, $histories): bool
    {
        return in_array($device->auto_reply_method, ['chatbot', 'all']) &&
            $histories->status != 'block';
    }

    private function processAiResponse($histories, TelegramProcessorService $processor, $fineTunnel, $settings): ?array
    {
        $message = $processor->getMessage();

        // Handle audio to text conversion
        if ($processor->getMessageType() == 'audio' && $processor->getFilePath()) {
            $audioText = $this->openAiService->checkAudioData(
                $processor->getFilePath(),
                $this->generalSetting->open_ai_key
            );
            if ($audioText['status']) {
                $message = $audioText['message'];
            }
        }

        if (!$message) {
            return null;
        }

        $conversations = $histories->details_desc->take($fineTunnel->history_limit)->sortBy('created_at');
        $dataTrainings = $fineTunnel->details;
        $imagePath = $processor->getMessageType() == 'image' ? $processor->getFilePath() : null;

        $intentResponse = $this->openAiService->detectIntent(
            $fineTunnel,
            $this->generalSetting->open_ai_key,
            $message,
            $conversations,
            $fineTunnel->model_ai,
            $imagePath
        );

        return $this->handleAiIntentResponse($intentResponse, $histories, $fineTunnel, $settings, $processor);
    }

    private function handleAiIntentResponse($intentResponse, $histories, $fineTunnel, $settings, $processor): ?array
    {
        if ($intentResponse->status() != 200) {
            return null;
        }

        $responseBody = json_decode($intentResponse->body());
        $intent = json_decode($responseBody->choices[0]->message->content);
        $creditUsed = $responseBody->usage->total_tokens ?? 0;

        // Normalize intent object
        if (is_object($intent) && isset($intent->properties)) {
            foreach ($intent->properties as $key => $value) {
                $intent->{$key} = $value;
            }
            unset($intent->properties);
        }

        $response = null;

        switch ($intent->intent) {
            case 'media':
                $response = $this->handleMediaIntent($intent, $histories);
                break;
            case 'check_shipping':
                $response = $this->handleShippingIntent($intent, $fineTunnel, $settings, $histories);
                break;
            case 'question':
                $response = $this->handleQuestionIntent($intent, $histories, $fineTunnel);
                break;
        }

        if ($response && $creditUsed > 0) {
            $this->updateCredits($creditUsed, $processor->getDevice(), $fineTunnel, $response['record'] ?? null);
        }

        return $response;
    }

    private function handleMediaIntent($intent, $histories): ?array
    {
        $mediaUrls = $intent->medias ?? [];
        $responses = [];

        foreach ($mediaUrls as $mediaUrl) {
            $mediaInfo = $this->getMediaInfo($mediaUrl);
            $messageType = $this->determineMessageTypeFromMime($mediaInfo['type']);

            $record = $histories->details()->create([
                'file_path' => $mediaUrl,
                'file_type' => $mediaInfo['type'],
                'file_size' => $mediaInfo['size'],
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'type' => $messageType,
                'is_follow_up' => 'yes',
                'credit_using' => 0,
            ]);

            $responses[] = [
                'message' => '',
                'file_path' => $mediaUrl,
                'from'      => 'reply',
                'record' => $record
            ];
        }

        return $responses[0] ?? null; // Return first response for now
    }

    private function handleShippingIntent($intent, $fineTunnel, $settings, $histories): ?array
    {
        $shippingConfig = $this->getShippingConfig($fineTunnel, $settings);

        if (!$shippingConfig['available']) {
            $message = 'Maaf kak 🙏, aku nggak bisa bantu cek ongkirnya langsung nih. Tapi kalau mau, aku bisa kasih cara atau link biar kakak bisa cek sendiri 😊';
        } else {
            $message = $this->processShippingCheck($intent, $fineTunnel, $settings);
        }

        $record = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' =>  $fineTunnel->follow_ups->count() > 0 ? 'no' : 'yes',
            'message' => $message,
        ]);

        return [
            'message' => $message,
            'from'      => 'reply',
            'record' => $record
        ];
    }

    private function handleQuestionIntent($intent, $histories, $fineTunnel): ?array
    {
        $message = $this->cleanText($intent->message);

        $record = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' =>  $fineTunnel->follow_ups->count() > 0 ? 'no' : 'yes',
            'message' => $message
        ]);

        return [
            'message' => $message,
            'from'      => 'reply',
            'record' => $record
        ];
    }

    // Keep existing helper methods with improvements
    private function getShippingConfig($fineTunnel, $settings): array
    {
        $hasPackageAccess = ($settings->package_active && $settings->package_active->cek_ongkir == 'yes') ||
            is_null($settings->merchant_id);

        $hasRequiredConfig = $fineTunnel->couriers->count() > 0 &&
            !empty($fineTunnel->zip_code) &&
            (int)$fineTunnel->weight > 0;

        return [
            'available' => $hasPackageAccess && $hasRequiredConfig,
            'region' => $fineTunnel->address ?? '',
            'courier_codes' => $fineTunnel->couriers->pluck('code')->unique()->implode(','),
            'weight' => (int)$fineTunnel->weight,
        ];
    }

    private function processShippingCheck($intent, $fineTunnel, $settings): string
    {
        $posCode = $intent->pos_code ?? null;
        $quantity = $intent->quantity ?? 1;
        $address = $intent->address ?? '';
        $message = $intent->message ?? '';

        if ($posCode === "" || $posCode === "null" || $posCode === null) {
            return $this->cleanText($message);
        }

        $apiKey = $this->generalSetting->cek_ongkir_option_api == 'sistem'
            ? $this->generalSetting->cek_ongkir_api
            : ($settings->cek_ongkir_api ?? '');

        $apiMethod = $this->generalSetting->ongkir_method;

        if ($apiMethod == 'rajaongkir') {
            return $this->processRajaOngkir($fineTunnel, $apiKey, $posCode, $message);
        } elseif ($apiMethod == 'biteship') {
            return $this->processBiteship($fineTunnel, $apiKey, $posCode, $quantity, $address, $message);
        }

        return 'Metode pengiriman tidak didukung.';
    }

    private function processRajaOngkir($fineTunnel, $apiKey, $posCode, $message): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode('|');

        $response = $this->rajaOngkirObserver->checkOngkir(
            $apiKey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices
        );

        if ($response->status() == 200) {
            $ongkirData = json_decode($response->body(), true);
            foreach ($ongkirData['data'] as $ongkir) {
                $message .= "- {$ongkir['name']} - {$ongkir['service']} ({$ongkir['etd']} hari): Rp " . number_format($ongkir['cost'], 0, ',', '.') . "\n";
            }
        } else {
            $message = 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        return $message;
    }

    private function processBiteship($fineTunnel, $apiKey, $posCode, $quantity, $address, $message): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode(',');

        $response = $this->biteshipService->checkOngkir(
            $apiKey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices,
            (int)$quantity
        );

        if ($response->status() == 200) {
            $ongkirData = json_decode($response->body(), true);

            $origin = $ongkirData['origin']['administrative_division_level_4_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_3_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_2_name'];

            $message = "📦 Detail Pengecekan Ongkos Kirim\n";
            $message .= "Jumlah barang: *{$quantity}*\n";
            $message .= "🚚 Pengiriman\n";
            $message .= "Dari: *{$origin}*\n";
            $message .= "Ke: *{$address}*\n";
            $message .= "Berikut detail ongkirnya:\n\n";

            foreach ($ongkirData['pricing'] as $ongkir) {
                $duration = $ongkir['duration'];
                $courier = $ongkir['courier_name'];
                $service = $ongkir['courier_service_name'];
                $price = number_format($ongkir['price'], 0, ',', '.');

                $message .= "- $courier - $service ($duration): Rp $price\n";
            }
        } else {
            Log::info('Biteship error: ' . $response->body());
            $message = 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        return $message;
    }

    private function processChatbotResponse($device, $message, $histories): ?array
    {
        // This would call your existing autoReplyMessage method
        $reply = $this->autoReplyMessage($device, $message, $histories);

        if ($reply['status']) {
            return [
                'message' => $reply['message'],
                'record' => $reply['record'] // You might want to create a record here too
            ];
        }

        return null;
    }

    private function autoReplyMessage($device, $message, $histories): array
    {
        $responses = [];
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $device->id . "',select_telegram)")->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        if ($chatBot->reply_method == 'text') {

            $record = $histories->details()->create([
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => 'yes',
                'message' => $chatBot->message
            ]);

            return array(
                'status'        => true,
                'record'        => $record,
                'message'       => $chatBot->message
            );
        }

        if ($chatBot->reply_method == 'template') {

            $file           = $chatBot->template->image != null ? asset($chatBot->template->image) : '';
            $messageText    = $chatBot->template->message ?? '';

            $record = $histories->details()->create([
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => 'yes',
                'message' => $messageText
            ]);

            return array(
                'status'        => true,
                'file_path'     => $file,
                'message'       => $messageText
            );
        }


        if ($chatBot->reply_method == 'image') {
            $responses = [];

            foreach ($chatBot->details as $detail) {
                $mediaInfo = $this->getMediaInfo($detail->url);
                $messageType = $this->determineMessageTypeFromMime($mediaInfo['type']);

                $record = $histories->details()->create([
                    'file_path' => $detail->url,
                    'file_type' => $mediaInfo['type'],
                    'file_size' => $mediaInfo['size'],
                    'history_chat_id' => $histories->id,
                    'from' => 'device',
                    'is_read' => 'yes',
                    'type' => $messageType,
                    'is_follow_up' => 'yes',
                    'credit_using' => 0,
                ]);

                $responses[] = [
                    'message' => '',
                    'file_path' => $detail->url,
                    'from'      => 'reply',
                    'record' => $record
                ];
            }

            return $responses[0] ?? null; // Return first response for now
        }


        // Placeholder - implement your chatbot logic here
        return ['status' => false];
    }

    private function getMediaInfo($url): array
    {
        try {
            $pathInfo = pathinfo($url);
            $headers = get_headers($url, 1);

            $size = isset($headers['Content-Length']) ? (int)$headers['Content-Length'] : null;
            $type = isset($headers['Content-Type']) ? $headers['Content-Type'] : null;

            return [
                'type' => $type,
                'size' => $size,
                'path' => $pathInfo['dirname'] . '/' . $pathInfo['basename'],
            ];
        } catch (\Exception $e) {
            Log::error('Media info error: ' . $e->getMessage());
            return ['type' => null, 'size' => null, 'path' => null];
        }
    }

    private function determineMessageTypeFromMime(string $mimeType): string
    {
        $typeMimeData = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/avi', 'video/mov', 'video/webm'],
            'audio' => ['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mpeg'],
            'document' => ['application/pdf', 'application/msword', 'text/plain']
        ];

        foreach ($typeMimeData as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $type;
            }
        }
        return 'text';
    }

    private function updateCredits(int $creditUsed, $device, $fineTunnel, $replyMessage): void
    {
        if ($creditUsed <= 0 || is_null($device->business->merchant ?? null)) {
            return;
        }

        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $totalCreditUsing = $this->calculateCreditUsage($creditUsed, $fineTunnel->model_ai, $setting);

        $this->deductCredits($device, $totalCreditUsing);

        if ($replyMessage) {
            $replyMessage->update(['credit_using' => $totalCreditUsing]);
        }
    }

    private function calculateCreditUsage(int $creditUsed, string $modelAi, $setting): int
    {
        $creditPer250Tokens = 1;
        $tokensPerCredit = $modelAi == 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($creditUsed / $tokensPerCredit) * $creditPer250Tokens;
    }

    private function deductCredits($device, int $totalCreditUsing): void
    {
        $packageCredit = $device->business->package_active->sisa_credit ?? 0;

        if ($packageCredit > 0) {
            $device->business->package_active->increment('using_credit_limit', $totalCreditUsing);
        } else {
            $device->business->package_active_topup->increment('using_credit_limit', $totalCreditUsing);
        }
    }

    private function cleanText($text): string
    {
        return trim(strip_tags($text));
    }
}
