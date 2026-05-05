<?php

namespace App\View\Components;

use App\Models\InternalSetting;
use App\Models\Setting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderComponent extends Component
{
   

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $internalSetting    = InternalSetting::first(['white_logo', 'logo', 'app_name','icon']);
        $settings           = Setting::withoutGlobalScopes()->where('merchant_id', my_user()->merchant_id)->first(['default_lang','is_online']);
        return view('components.header-component', compact('settings','internalSetting'));
    }
}
