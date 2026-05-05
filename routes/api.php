<?php

use App\Http\Controllers\Api\InstagramController;
use App\Http\Controllers\Api\LiveChatController;
use App\Http\Controllers\Api\MessengerController;
use App\Http\Controllers\Api\TelegramCallbackController;
use App\Http\Controllers\Api\CreditApiController;
use App\Http\Controllers\Api\WabaCallbackController;
use App\Http\Controllers\Api\WabaMessageController;
use App\Http\Controllers\Api\WhatsappCallbackController;
use App\Http\Controllers\Api\WhatsappMessageController;
use App\Http\Controllers\Api\WhatsappMiscController;
use App\Http\Controllers\Public\PublicTicketController;
use App\Http\Controllers\Starter\StarterController;
use App\Http\Controllers\Store\GroupScrappingController;
use App\Http\Controllers\Store\WhatsappContactScrappingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;


header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

// Public API Routes (No Authentication Required)
Route::prefix('public')->group(function () {
    Route::get('tickets/{ticketId}', [PublicTicketController::class, 'show']);
});

Route::prefix('whatsapp')->middleware(['throttle:60,1'])->group(function () {
    Route::post("callback/{device_id}", [WhatsappCallbackController::class, 'callBackWhatsapp']);
    Route::post('send-message', [WhatsappMessageController::class, 'message']);
    Route::post('set-status/{device}/{status}', [WhatsappMessageController::class, 'setStatusDevice']);
    Route::post('to-me/{device_id}', [WhatsappCallbackController::class, 'callbackMe']);
    Route::prefix('chats')->group(function () {
        Route::get('/{device}', [WhatsappMessageController::class, 'getChatList']);
        Route::get('contacts/{device}', [WhatsappMessageController::class, 'getContactList']);
        Route::get('detail/{device}/{chatId}', [WhatsappMessageController::class, 'getChatDetails']);
    });

    Route::prefix('misc')->group(function () {
        Route::post("read-messages/{id}", [WhatsappMiscController::class, 'readMessage']);
        Route::post("delete-message/{id}", [WhatsappMiscController::class, 'deleteForMe']);
        Route::post("delete-everyone/{id}", [WhatsappMiscController::class, 'deleteEveryOne']);
        Route::post("download-media/{id}", [WhatsappMiscController::class, 'downloadMedia']);
        Route::post("get-profile/{id}", [WhatsappMiscController::class, 'getPhotoProfile']);
        Route::post("mark-message/{id}", [WhatsappMiscController::class, 'markMessage']);
        Route::post("delete-chat/{id}", [WhatsappMiscController::class, 'deleteChats']);
    });
});

Route::prefix('waba')->middleware(['throttle:120,1'])->group(function () {
    Route::get('webhook', [WabaCallbackController::class, 'genericWebhookVerify']);
    Route::post('webhook', [WabaCallbackController::class, 'genericWebhookCallback']);
    Route::get("send-message", [WabaCallbackController::class, 'checkTokenVerify']);
    Route::get('callback-url/{settings}', [WabaCallbackController::class, 'checkTokenVerify']);
    Route::post('callback-url/{settings}', [WabaCallbackController::class, 'callbackMessage']);

    Route::prefix('messages')->group(function () {
        Route::post('simple', [WabaMessageController::class, 'sendMessage']);
        Route::post('template', [WabaMessageController::class, 'sendTemplateMessage']);
    });
});

Route::prefix('chats')->middleware('cors')->group(function () {
    Route::get('information', [LiveChatController::class, 'getInformation']);
    Route::get('histories/{history}', [LiveChatController::class, 'chatHistories']);
    Route::post('save-or-update/{chat}', [LiveChatController::class, 'checkForChat']);

    Route::prefix('message')->group(function () {
        Route::post('received/{history}', [LiveChatController::class, 'sendMessage']);
        Route::post('callback/{history}', [LiveChatController::class, 'callbackMessage']);
    });
});

Route::prefix('payments')->group(function () {
    Route::post('callback', [StarterController::class, 'callbackPayment']);
});

Route::prefix('scrapping')->group(function () {
    Route::post('callback/{id}/{business}', [GroupScrappingController::class, 'callback']);
    Route::post('contacts/{id}', [WhatsappContactScrappingController::class, 'callback']);
    Route::post('groups/{id}', [GroupScrappingController::class, 'groupCallback']);
});

Route::prefix('integration')->group(function () {
    Route::post('checking-device', [WhatsappMessageController::class, 'checkingDevice']);
    Route::get('get-templates', [WhatsappMessageController::class, 'getTemplates']);
});

Route::prefix('telegram')->group(function () {
    Route::get('callback/{telegram}', [TelegramCallbackController::class, 'checkingCallback']);
    Route::post('callback/{telegram}', [TelegramCallbackController::class, 'callbackTelegram']);
});

Route::prefix('instagram')->group(function () {
    Route::get('callback', [InstagramController::class, 'handle']);
    Route::post('callback', [InstagramController::class, 'handle']);
});

Route::prefix('messenger')->group(function () {
    Route::get('callback', [MessengerController::class, 'handle']);
    Route::post('callback', [MessengerController::class, 'handle']);
});

// AI Credit API
Route::post('credit', [CreditApiController::class, 'getCredit']);
