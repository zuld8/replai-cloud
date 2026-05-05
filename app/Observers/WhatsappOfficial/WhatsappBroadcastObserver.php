<?php

namespace App\Observers\WhatsappOfficial;

use App\Models\Blash\BlashWhatsapp;
use Illuminate\Http\Request;

class WhatsappBroadcastObserver
{
    public function getData(Request $request, $wabaID)
    {
        return BlashWhatsapp::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->district ? $q->where("district_id", $request->district) : '';
        })->where(function ($q) use ($request) {
            return $request->city ? $q->where("city_id", $request->city) : '';
        })->where(function ($q) use ($request) {
            return $request->category ? $q->where("category_id", $request->category) : '';
        })->where(function ($q) use ($request) {
            return $request->template ? $q->where("template_id", $request->template) : '';
        })->where('waba', 'yes')->where('meta_account_id', $wabaID)->orderBy('created_at', 'desc');
    }

    public function createData(Request $request, $wabaID, $file = '')
    {
        return BlashWhatsapp::create([
            'category_id'           => $request->category,
            'city_id'               => $request->city,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'template_id'           => $request->template,
            'delay'                 => $request->delay ?? 60,
            'waba'                  => 'yes',
            'status'                => 'pending',
            'meta_account_id'       => $wabaID,
            'file'                  => $file != '' ? $file : null,
            'metadata'              => $request->metadata,
            'whatsapp_sender_notif' => $request->whatsapp_sender,
            'devices'               => $request->devices,
            'stop_sending'          => $request->stop_sending ?? 0,
            'rest_sending'          => $request->rest_sending ?? 0,
            'use'                   => 'whatsapp',
        ]);
    }

    public function updateData(Request $request, BlashWhatsapp $blash, $file = '')
    {
        $blash->update([
            'category_id'           => $request->category,
            'city_id'               => $request->city,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'template_id'           => $request->template,
            'delay'                 => $request->delay ?? 60, 
            'file'                  => $file != '' ? $file : $blash->file,
            'metadata'              => $request->metadata,
            'whatsapp_sender_notif' => $request->whatsapp_sender,
            'devices'               => $request->devices,
            'stop_sending'          => $request->stop_sending ?? 0,
            'rest_sending'          => $request->rest_sending ?? 0,
            'use'                   => 'whatsapp',
        ]);
    }

    public function deleteData(BlashWhatsapp $blash)
    {
        $blash->details()->delete();
        $blash->delete();
        return redirect()->back()->with(['flash'    => 'Berhasil menghapus data']);
    }
}
