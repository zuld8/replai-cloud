<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\QuickReply;
use Illuminate\Http\Request;

class QuickReplyObserver
{
    public function getData(Request $request)
    {
        return QuickReply::where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('content', 'like', '%' . $request->name . '%') : '';
        })->orderBy('content', 'asc');
    }

}
