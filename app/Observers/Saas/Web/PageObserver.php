<?php

namespace App\Observers\Saas\Web;

use App\Models\Cms\Page;
use Illuminate\Http\Request;

class PageObserver
{

    public function getData(Request $request)
    {
        return Page::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function cerateData(Request $request)
    {
        return Page::create([
            'name'              => $request->page_name,
            'page'              => $request->page_type,
            'content'           => '-'
        ]);
    }

    public function updateData(Request $request, Page $page)
    {
        $body       = [
            'components'    => $request->get('laravel-grapesjs-components'),
            'styles'        => $request->get('laravel-grapesjs-styles'),
            'css'           => $request->get('laravel-grapesjs-css'),
            'html'          => $request->get('laravel-grapesjs-html'),
        ];

        $page->update([
            'content'       => $body
        ]);
    }

    public function deleteData(Page $page)
    {
        $page->delete();
    }
}
