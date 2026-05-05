<?php

namespace App\View\Components\Starter;

use App\Models\InternalSetting;
use App\Models\Setting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderComponent extends Component
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
        $internalSetting    = InternalSetting::first(['white_logo', 'logo', 'app_name','icon']);
        $settings           = Setting::withoutGlobalScopes()->where('merchant_id', my_user()->merchant_id)->first(['default_lang']);
        return view('components.starter.header-component', compact('internalSetting', 'settings'));
    }
}
