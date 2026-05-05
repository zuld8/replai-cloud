<?php

namespace App\Observers;

use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use Illuminate\Http\Request;

class WhatsappObserver
{
    public function getData(Request $request)
    {
        return WhatsappKeyAccount::where(function ($q) use ($request) {
            return $request->phone ? $q->where('phone', 'like', '%' . $request->phone . '%') : '';
        })->orderBy('phone', 'asc');
    }

    public function createData(Request $request)
    {
        return WhatsappKeyAccount::create([
            'phone'                 => $request->phone,
            'whatsapp_key'          => $request->key,
            'whatsapp_session'      => $request->session,
            'limit_per_day'         => $request->limit,
        ]);
    }

    public function updateData(Request $request, WhatsappKeyAccount $whatsapp)
    {
        $whatsapp->update([
            'phone'                 => $request->phone,
            'whatsapp_key'          => $request->key,
            'whatsapp_session'      => $request->session,
            'limit_per_day'         => $request->limit,
        ]);
    }

    public function deleteData(WhatsappKeyAccount $whatsapp)
    {
        $whatsapp->delete();
    }

   
}
