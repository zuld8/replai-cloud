<?php

namespace App\View\Components;

use App\Models\ChatBot\HistoryChat;
use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class MenuComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $internalSetting    = InternalSetting::first(['white_logo', 'logo', 'app_name', 'icon']);
        // OPTIMIZED: Pindah dari history_chat_details (2.9M rows) ke history_chats
        // pakai kolom unread_count (sudah maintained oleh observer) + Redis cache 60 detik.
        // Query lama scan 2.9M baris dengan 6 OR EXISTS = 60-80 detik per request!
        $userId = my_user()->id ?? null;
        $bizId  = my_business() ?? null;
        $chatsNotRead = 0;
        if ($userId && $bizId) {
            $chatsNotRead = Cache::remember("menu_unread_{$bizId}_{$userId}", 60, function () use ($bizId, $userId) {
                return HistoryChat::where('business_id', $bizId)
                    ->where('status', '!=', 'block')
                    ->where('unread_count', '>', 0)
                    ->where(function ($query) use ($userId) {
                        $query->whereHas('device.agents', fn($q) => $q->where('user_id', $userId))
                              ->orWhereHas('waba.agents', fn($q) => $q->where('user_id', $userId))
                              ->orWhereHas('livechat.agents', fn($q) => $q->where('user_id', $userId))
                              ->orWhereHas('telegram.agents', fn($q) => $q->where('user_id', $userId))
                              ->orWhereHas('instagram.agents', fn($q) => $q->where('user_id', $userId))
                              ->orWhereHas('messenger.agents', fn($q) => $q->where('user_id', $userId));
                    })
                    ->count();
            });
        }

        return view('components.menu-component', compact('internalSetting', 'chatsNotRead'));
    }
}
