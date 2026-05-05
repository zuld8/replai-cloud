<?php

namespace App\Observers\Store;

use App\Models\Store\WhatsappGroup;
use Illuminate\Http\Request;

class WhatsappGroupObserver
{
    public function getData(Request $request)
    {
        return WhatsappGroup::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->devices ? $q->whereIn('device_id', $request->devices) : '';
        })->orderBy('name', 'asc');
    }
}
