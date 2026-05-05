<?php

namespace App\Observers\Blash;

use App\Models\Blash\BlashDetail;
use App\Models\Blash\BlashWhatsapp;
use Illuminate\Http\Request;

class BlashDetailObserver
{ 
    public function getData(Request $request, BlashWhatsapp $blash)
    {
        $query = BlashDetail::where("blash_whatsapp_id", $blash->id);

        if ($request->name) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('store', function ($sq) use ($request) {
                    $sq->withoutGlobalScopes()->where('name', 'like', '%' . $request->name . '%');
                })->orWhereHas('group', function ($gq) use ($request) {
                    $gq->where('name', 'like', '%' . $request->name . '%');
                })->orWhere('phone', 'like', '%' . $request->name . '%');
            });
        }

        return $query->orderBy('created_at', 'desc');
    }
    
}
