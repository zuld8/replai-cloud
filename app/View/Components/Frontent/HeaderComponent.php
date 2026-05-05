<?php

namespace App\View\Components\Frontent;

use App\Models\Cms\FooterLink;
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
        $settings   = InternalSetting::first(['web_template', 'logo', 'app_name', 'blog', 'pricing', 'contact', 'register','white_logo']);
        $links      = FooterLink::where('position', 1)->orderBy('order_position', 'asc')->get(['name', 'url']);
        return view("components.frontent.{$settings->web_template}.header-component", compact('settings', 'links'));
    }
}
