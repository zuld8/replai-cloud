<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WabaSidebarUpdateComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public $idwaba;
    public function __construct($idwaba)
    {
        $this->idwaba       = $idwaba;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.waba-sidebar-update-component');
    }
}
