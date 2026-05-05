<?php

namespace App\View\Components\Frontent;

use App\Models\InternalSetting;
use App\Models\Package\Package;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PricingContentComponent extends Component
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
        $setting    = InternalSetting::first(['web_template']);
        $pricing    = Package::orderBy('price','asc')->get();
        return view("components.frontent.{$setting->web_template}.pricing-content-component", compact('pricing'));
    }
}
