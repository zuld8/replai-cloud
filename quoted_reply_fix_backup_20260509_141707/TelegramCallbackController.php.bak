<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveChat\HistoryChatResources;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\HistoryChat;
use App\Models\Setting;
use App\Models\TelegramKey;
use App\Models\User;
use App\Observers\ChatBot\BiteshipServiceObserver;
use App\Observers\ChatBot\ChatPdfServiceObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\HistoryChatObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\ChatBot\RajaOngkirServiceObserver;
use App\Observers\Store\StoreObserver;
use App\Services\Platform\Telegram\TelegramHandlerService;
use App\Services\Platform\Telegram\TelegramProcessorService;
use App\Services\Platform\TelegramService;
use App\Supports\MimeTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramCallbackController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Telegram Callback Api Controller
    |--------------------------------------------------------------------------
    */

    protected $openAiServiceObserver;
    protected $telegramService;
    protected $geminiAiServiceObserver;
    protected $chatPdfServiceObserver;
    protected $historyChatObserver;
    protected $storeObserver;
    protected $rajaOngkirObserver;
    protected $biteshipServiceObserver;
    protected $generalSetting;
    protected $typeMimeData;

    public function __construct(
        OpenAiServiceObserver $openAiServiceObserver,
        TelegramService $telegramService,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        HistoryChatObserver $historyChatObserver,
        ChatPdfServiceObserver $chatPdfServiceObserver,
        StoreObserver $storeObserver,
        RajaOngkirServiceObserver $rajaOngkirObserver,
        BiteshipServiceObserver $biteshipServiceObserver
    ) {
        $this->openAiServiceObserver        = $openAiServiceObserver;
        $this->telegramService      = $telegramService;
        $this->geminiAiServiceObserver      = $geminiAiServiceObserver;
        $this->historyChatObserver          = $historyChatObserver;
        $this->chatPdfServiceObserver       = $chatPdfServiceObserver;
        $this->storeObserver                = $storeObserver;
        $this->rajaOngkirObserver           = $rajaOngkirObserver;
        $this->biteshipServiceObserver      = $biteshipServiceObserver;
        $this->generalSetting               = Setting::where('merchant_id', null)->first(['open_ai_key', 'ai_option', 'cek_ongkir_option_api', 'cek_ongkir_api', 'ongkir_method']);
        $this->typeMimeData                 = MimeTypes::TYPE_MAP;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Checking Callback / Webhook
    |--------------------------------------------------------------------------
    */
    public function checkingCallback(Request $request, TelegramKey $telegram)
    {
        return response()->json(['status' => 'ok']);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Telegram Callback / Webhook
    |--------------------------------------------------------------------------
    */

    public function callbackTelegram(Request $request, TelegramKey $telegram)
    {
 
        if (!$telegram) {
            return response()->json(['status' => 'error', 'message' => 'Device not found'], 400);
        }

        try {
            $messageProcessor = new TelegramProcessorService($request->all(), $telegram, $this->typeMimeData);

            // Early validation
            if (!$messageProcessor->isValidMessage()) {
                return response()->json(['status' => 'ok']);
            }

            DB::transaction(function () use ($messageProcessor) {
                $this->processMessage($messageProcessor);
                return response()->json(['status' => 'ok']);
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Telegram callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function processMessage(TelegramProcessorService $processor): bool
    {
        $settings = $this->getBusinessSettings($processor->getDevice());
        $histories = $this->getOrCreateHistory($processor);

        if (!$this->shouldProcessMessage($histories, $processor->getDevice(), $settings)) {
            return false;
        }

        // Handle file downloads if needed
        if ($processor->hasFile()) {
            $processor->downloadAndProcessFile();
        }

        // Create user message record
        $userMessage = $this->createUserMessage($histories, $processor);

        // Process different types of responses
        // 🔥 PASS GEMINI OBSERVER HERE
        $responseHandler = new TelegramHandlerService(
            $this->telegramService,
            $this->openAiServiceObserver,
            $this->geminiAiServiceObserver, // ← ADD THIS
            $this->rajaOngkirObserver,
            $this->biteshipServiceObserver,
            $this->generalSetting
        );

        $responses = $responseHandler->processResponse($histories, $processor, $settings);

        // Send responses
        $this->sendResponse($processor->getDevice(), $histories, $responses, $userMessage);

        return true;
    }

    private function getBusinessSettings($device): ?object
    {
        return Setting::where('id', $device->business_id)
            ->first(['history_ai_chat_option', 'history_ai_chat', 'is_online', 'id', 'cek_ongkir_api']);
    }

    private function getOrCreateHistory(TelegramProcessorService $processor): object
    {
        $fromNumber = $processor->getFromNumber();
        $device = $processor->getDevice();
        $data = $processor->getData();

        $histories = $this->historyChatObserver->getByNumber(
            'personal',
            $fromNumber,
            null,
            'telegram',
            null,
            null,
            $device->id
        );

        if ($histories) {
            if ($histories->status == 'block') {
                throw new \Exception('User is blocked');
            }

            if ($histories->status != 'open') {
                $histories->update(['status' => 'open']);
            }
        } else {
            $userName = $data['message']['from']['first_name'] ?? $fromNumber;
            $histories = $this->historyChatObserver->createData(
                null,
                'personal',
                $fromNumber,
                null,
                $device->merchant_id,
                $device->business_id,
                $userName,
                'telegram',
                null,
                null,
                null,
                $device->id
            );
        }

        return $histories;
    }

    private function shouldProcessMessage($histories, $device, $settings): bool
    {
        // Check if message already processed
        if (
            $histories && $histories->details()
            ->where('messageid', request()->input('message.message_id'))
            ->exists()
        ) {
            return false;
        }

        // Check daily limits
        if (
            $device->daily_limit == 'yes' &&
            $device->daily_send >= $device->limit_per_day
        ) {
            return false;
        }

        // Check time restrictions
        return $this->checkingTimeAutoReply($device);
    }

    private function createUserMessage($histories, TelegramProcessorService $processor): object
    {
        // Ensure store exists
        $this->ensureStoreExists($histories, $processor->getDevice());

        // Mark previous messages as followed up
        $histories->details()
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->update([
                'is_follow_up' => 'yes',
                'follow_up_id' => null
            ]);

        return $histories->details()->create([
            'file_path' => $processor->getFilePath(),
            'file_type' => $processor->getFileType(),
            'file_size' => $processor->getFileSize(),
            'type' => $processor->getMessageType(),
            'history_chat_id' => $histories->id,
            'from' => 'user',
            'message' => $processor->getMessage(),
            'remotejid' => request()->from,
            'messageid' => $processor->getMessageId()
        ]);
    }

    private function ensureStoreExists($histories, $device): void
    {
        $stores = $this->storeObserver->checkByNumber($histories->from_number, $device->business_id);

        if (!$stores) {
            $stores =  $this->storeObserver->createByHistory($histories);
        }

        if (empty($histories->store_id)) {
            $histories->update(['store_id'    => $stores->id]);
        }
    }

    private function sendResponse($device, $histories, $responses, $userMessage): void
    {

        $welcome    = null;
        $reply      = null;
        foreach ($responses as $response) {
            if (empty($response['message']) && empty($response['file_path'])) {
                continue;
            }


            if ($response['from'] == 'device') {
                $reply  = $response;
            }

            $this->telegramService->sendMessage(
                $device,
                $histories->from_number,
                $response['message'] ?? '',
                $response['file_path'] ?? null
            );
        }


        // Trigger real-time events if needed
        // if (method_exists($this, 'triggerEmit')) {
        //     $this->triggerEmit($reply, $userMessage, $welcome);
        // }

        $this->triggerEmit($reply, $userMessage, $welcome);
    }



    function cleanText($text)
    {
        // Pisahkan kata sebelum link dalam format: Kata(http://...)
        $text = preg_replace('/([^\(]+)\((https?:\/\/[^\s\)]+)\)/', '$1 $2', $text);

        // Hapus karakter penutup seperti ] yang menempel di akhir URL
        $text = preg_replace('/(https?:\/\/[^\s\]]+)\]/', '$1', $text);

        // Simpan URL sebagai placeholder
        preg_match_all('/https?:\/\/[^\s]+/', $text, $matches);
        $urls = $matches[0];
        foreach ($urls as $index => $url) {
            $text = str_replace($url, "__URL{$index}__", $text);
        }

        // Ubah ** menjadi *
        $text = str_replace('**', '*', $text);

        // Hapus simbol tidak penting: #, !, {}, [], ()
        $text = preg_replace('/[\#\!\{\}\[\]\(\)]/', '', $text);

        // Hapus karakter non-alfanumerik berdiri sendiri (kecuali -)
        $text = preg_replace('/(?<=\s)[^\p{L}\p{N}-](?=\s)/u', '', $text);

        // Bersihkan per baris
        $text = preg_replace_callback('/^.*$/m', function ($lineMatches) {
            $line = $lineMatches[0];

            return preg_replace_callback('/\S+/', function ($matches) use ($line) {
                $word = $matches[0];

                // Pertahankan URL
                if (preg_match('/^__URL\d+__$/', $word)) return $word;

                // Pertahankan - di awal baris (untuk list)
                if (trim($line)[0] === '-' && $word === '-') return $word;

                // Pertahankan @username jika valid
                if (!preg_match('/^@[\p{L}\p{N}_-]+$/u', $word)) {
                    $word = str_replace('@', '', $word);
                }

                // Pertahankan ? dan : hanya jika di akhir kata
                if (strpos($word, '?') !== false && substr($word, -1) !== '?') {
                    $word = str_replace('?', '', $word);
                }
                if (strpos($word, ':') !== false && substr($word, -1) !== ':') {
                    $word = str_replace(':', '', $word);
                }

                // Pertahankan angka diikuti % (misal: 30%, 50 %)
                if (preg_match('/^\d+\s?%$/', $word)) return $word;

                // Pertahankan simbol mata uang diikuti angka (misal: $50, Rp 1000)
                if (preg_match('/^[$£€¥Rp]+\s?\d+([\.,]\d+)*$/', $word)) return $word;

                // Bersihkan karakter asing, izinkan simbol penting dan emoji
                return preg_replace(
                    '/[^\p{L}\p{N}\.\,\;\_\-\?\:\%\/\$\@\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u',
                    '',
                    $word
                );
            }, $line);
        }, $text);

        // Kembalikan URL dari placeholder
        $seen = [];
        foreach ($urls as $index => $url) {
            $placeholder = "__URL{$index}__";
            if (!in_array($url, $seen)) {
                $text = str_replace($placeholder, $url, $text);
                $seen[] = $url;
            } else {
                $text = str_replace($placeholder, '', $text);
            }
        }

        return $text;
    }


    public function triggerEmit($replyForEmit, $userForEmit, $welcomeForEmit)
    {

        $expressUrl = config('services.express.url') . '/trigger-whatsapp';

        if ($welcomeForEmit) {
            Http::post($expressUrl, HistoryChatResources::make($welcomeForEmit));
        }

        if ($userForEmit) {
            // Kirim request pertama
            $response = Http::post($expressUrl, HistoryChatResources::make($userForEmit));

            // Jika request pertama berhasil, baru kirim request kedua
            if ($response->successful() && $replyForEmit) {
                Http::post($expressUrl, HistoryChatResources::make($replyForEmit));
            }
        } elseif ($replyForEmit) {
            // Jika $userForEmit tidak ada, langsung kirim request kedua
            Http::post($expressUrl, HistoryChatResources::make($replyForEmit));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Validation For Auto Reply Days and Time
    |--------------------------------------------------------------------------
    */

    public function checkingTimeAutoReply(TelegramKey $device)
    {

        /**
         * Check For Activation Days 
         */

        if ($device->auto_reply_certain_day == 'yes') {
            if ($device->days != null) {
                $day        = date("D");
                $getVoucher = TelegramKey::where("id", $device->id)->whereRaw("find_in_set('" .  $day . "',days)")->count(); // Check Day in This Auto Reply

                // If Auto Reply Not Active for this day
                if ($getVoucher == 0) {
                    return false;
                }
            }
        }


        /**
         * Checking For Activation Time
         */

        if ($device->auto_reply_certain_time == 'yes') {
            if ($device->start_time != null) {
                if ($device->start_time > date("H:i")) {
                    return false;
                }

                if ($device->end_time < date("H:i")) {
                    return false;
                }
            }
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Whatsapp Message For Use Auto Reply
    |--------------------------------------------------------------------------
    */

    public function autoReplyMessage(TelegramKey $device, $message, $name = '', $from)
    {
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $device->id . "',select_telegram)")->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        if ($chatBot->reply_method == 'text') {
            return array(
                'status'        => true,
                'message_text'  => $chatBot->message,
                'message'       => array(
                    'text'          => $chatBot->message
                )
            );
        }

        if ($chatBot->reply_method == 'template') {

            $file           = $chatBot->template->image != null ? asset($chatBot->template->image) : '';
            $messageText    = $chatBot->template->message ?? '';

            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'message'       => array(
                    'text'          => $messageText
                )
            );
        }

        if ($chatBot->reply_method == 'image') {
            foreach ($chatBot->details as $detail) {
                $this->telegramService->sendMessage($device, $from, '', $detail->url);
            }

            return array(
                'status'    => true,
                'message'   => array(
                    'text'      => ''
                )
            );
        }
    }




    /*
    |--------------------------------------------------------------------------
    | 5. Any Auto Reply
    |--------------------------------------------------------------------------
    */

    public function anyAutoReply(TelegramKey $device, $name)
    {

        if ($device->reply_method == 'text') {

            $message = str_replace(
                ['{whatsapp_name}'],
                [$name],
                $device->reply_text
            );

            return array(
                'status'        => true,
                'message_text'  => $message,
                'message'       => array(
                    'text'          => $message
                )
            );
        }

        if ($device->reply_method == 'template') {

            $file           = $device->template->image != null ? asset($device->template->image) : '';
            $messageText    = $device->template->text ?? '';
            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'message'       => null
            );
        }
    }

    public function sendCustomWebHook(Request $request, TelegramKey $device)
    {

        $request_from   = explode('@', $request->from);
        $request_from   = $request_from;

        return Http::accept('application/json')->post($device->webhook, [
            'device_key'    => $device->id,
            'name'          => $request->from_name,
            'from'          => $request_from,
            'message'       => $request->message,
            'type'          => $request->type
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | 7. Send Message To Agent
    |--------------------------------------------------------------------------
    */

    public function sendToAgent(HistoryChat $history, $incomingMessage, User $user, TelegramKey $device)
    {
        $lines = [
            "👋 *Halo Agent {$user->name}*,",
            "",
            "Kami menerima pesan dari pelanggan:",
            "*{$history->name}*",
            "",
            "📩 *Pesan:*",
            $incomingMessage,
            "",
            "Mohon segera ditanggapi. Terima kasih 🙏"
        ];

        $formattedMessage = implode(PHP_EOL, $lines);

        // $this->telegramService->sendMessage(
        //     $user->phone,
        //     $device->id,
        //     $formattedMessage,
        //     '',
        //     'description',
        //     null,
        //     false,
        //     null
        // );
    }
}
