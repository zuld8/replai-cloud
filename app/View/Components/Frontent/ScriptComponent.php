<?php

namespace App\View\Components\Frontent;

use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ScriptComponent extends Component
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
        $settings = InternalSetting::first(['web_template']);
        return view("components.frontent.{$settings->web_template}.script-component");
    }
}
