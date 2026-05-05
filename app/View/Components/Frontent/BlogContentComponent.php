<?php

namespace App\View\Components\Frontent;

use App\Models\Cms\Blog;
use App\Models\InternalSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BlogContentComponent extends Component
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
        $blogs      = Blog::orderBy('created_at', 'desc')->limit(3)->get(['slug', 'name', 'thumbnail', 'created_at', 'category_id', 'meta_description']);
        return view("components.frontent.{$setting->web_template}.blog-content-component", compact('blogs'));
    }
}
