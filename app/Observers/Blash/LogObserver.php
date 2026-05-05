<?php

namespace App\Observers\Blash;

use App\Models\Log;
use Illuminate\Http\Request;

class LogObserver
{
    public function getData(Request $request, String $type = 'whatsapp')
    {
        return Log::where(function ($q) use ($request) {
            return $request->name ? $q->where('description', 'like', '%' . $request->name . '%')->orWhere('error', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where("type", $type) : '';
        })->orderBy('created_at', 'desc');
    }

    public function getDataForAdmin(Request $request, String $type = 'whatsapp')
    {
        return Log::withoutGlobalScopes()->where(function ($q) use ($request) {
            return $request->name ? $q->where('description', 'like', '%' . $request->name . '%')->orWhere('error', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where("type", $type) : '';
        })->orderBy('created_at', 'desc');
    }
}
