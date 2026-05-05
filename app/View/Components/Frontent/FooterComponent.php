<?php

namespace App\View\Components\Frontent;

use App\Models\Cms\FooterLink;
use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FooterComponent extends Component
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
        $setting    = InternalSetting::first(['web_template', 'logo', 'app_name', 'footer_description', 'footer_web', 'footer_1', 'footer_2', 'footer_3', 'white_logo','copyright']);
        $links      = FooterLink::where('position', 2)->orderBy('order_position', 'asc')->get(['name', 'url']);
        $links2     = FooterLink::where('position', 3)->orderBy('order_position', 'asc')->get(['name', 'url']);
        $links3     = FooterLink::where('position', 4)->orderBy('order_position', 'asc')->get(['name', 'url']);
        return view("components.frontent.{$setting->web_template}.footer-component", compact('setting', 'links', 'links2', 'links3'));
    }
}
