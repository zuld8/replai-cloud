<?php

namespace App\View\Components;

use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MetaComponent extends Component
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
        $setting    = InternalSetting::first(['app_name', 'logo', 'meta_keyword', 'meta_description', 'icon']);
        return view('components.meta-component', compact('setting'));
    }
}
