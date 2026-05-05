<?php

namespace App\View\Components\ChatApp;

use App\Models\InternalSetting;
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
        $setting = InternalSetting::first(['logo', 'white_logo']);
        return view('components.chat-app.header-component', compact('setting'));
    }
}
