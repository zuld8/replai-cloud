<?php

namespace App\View\Components\Admin;

use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarComponent extends Component
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
        $internalSetting    = InternalSetting::first(['white_logo', 'logo', 'app_name']);
        return view('components.admin.sidebar-component', compact('internalSetting'));
    }
}
