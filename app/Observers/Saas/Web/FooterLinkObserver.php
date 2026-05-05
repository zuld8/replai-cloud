<?php

namespace App\Observers\Saas\Web;

use App\Models\Cms\FooterLink;
use Illuminate\Http\Request;

class FooterLinkObserver
{
    public function getData(Request $request)
    {
        return FooterLink::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('order_position', 'asc');
    }

    public function createData(Request $request)
    {
        return FooterLink::create([
            'url'               => $request->url,
            'position'          => $request->position,
            'name'              => $request->name,
            'order_position'    => $request->order_position
        ]);
    }

    public function updateData(Request $request, FooterLink $link)
    {
        $link->update([
            'url'               => $request->url,
            'position'          => $request->position,
            'name'              => $request->name,
            'order_position'    => $request->order_position
        ]);
    }

    public function deleteData(FooterLink $link)
    {
        $link->delete();
    }
}
