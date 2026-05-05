<?php

namespace App\View\Components;

use App\Models\Admin\License;
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
        $license    = License::first(['version_name', 'version_code']);
        $settings   = InternalSetting::first(['copyright']);
        return view('components.footer-component', compact('settings', 'license'));
    }
}
