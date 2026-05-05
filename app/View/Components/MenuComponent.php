<?php

namespace App\View\Components;

use App\Models\ChatBot\HistoryChatDetail;
use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
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
        $chatsNotRead       = HistoryChatDetail::where('is_read', 'no')->where('from', 'user')->whereHas('history', function ($q) {
            return $q->where('business_id', my_business())->where('status', '!=', 'block');
        })->whereHas('history', function ($query) {
            $userId = my_user()->id;
            $query->whereHas('device.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('waba.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('livechat.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('telegram.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('instagram.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('messenger.agents', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })->count();

        return view('components.menu-component', compact('internalSetting', 'chatsNotRead'));
    }
}
