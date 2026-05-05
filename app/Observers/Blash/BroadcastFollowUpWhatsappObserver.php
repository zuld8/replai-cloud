<?php

namespace App\Observers\Blash;

use App\Models\Blash\BlashWhatsapp;
use Illuminate\Http\Request;

class BroadcastFollowUpWhatsappObserver
{
    public function getData(Request $request, String $type = 'whatsapp_follow_up')
    {
        return BlashWhatsapp::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->category ? $q->where("category_id", $request->category) : '';
        })->where(function ($q) use ($request) {
            return $request->template ? $q->where("template_id", $request->template) : '';
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where("use", $type) : '';
        })->where('waba', 'no')->orderBy('created_at', 'desc');
    }

    public function createData(Request $request, String $type = 'whatsapp_follow_up')
    {

        return BlashWhatsapp::create([
            'name'                  => $request->name,
            'delay'                 => $request->delay ?? 60,
            'devices'               => implode(",", $request->devices),
            'whatsapp_sender_notif' => $request->whatsapp_sender_notif,
            'schedule_frequency'    => $request->schedule_frequency,
            'days'                  => !empty($request->schedule_days) ? implode(",", $request->schedule_days) : null,
            'schedule'              => $request->schedule_date,
            'month'                 => $request->schedule_month,
            'yearly'                => $request->schedule_yearly_date,
            'time'                  => $request->schedule_time,
            'start_date'            => $request->start_date,
            'end_date'              => $request->end_date,
            'broadcast_method'      => $request->broadcast_method,
            'ai_prompt'             => $request->broadcast_method == 'ai' ? $request->ai_prompt : null,
            'template_id'           =>  $request->broadcast_method == 'template' ?  $request->template : null,
            'labels'                => !empty($request->label) ? implode(",", $request->label) : null,
            'category_id'           => $request->category ?? null,
            'use'                   => $type,

        ]);
    }

    public function updateData(Request $request, BlashWhatsapp $blash)
    {

        $blash->update([
            'name'                  => $request->name,
            'delay'                 => $request->delay ?? 60,
            'devices'               => implode(",", $request->devices),
            'whatsapp_sender_notif' => $request->whatsapp_sender_notif,
            'schedule_frequency'    => $request->schedule_frequency,
            'days'                  => !empty($request->schedule_days) ? implode(",", $request->schedule_days) : null,
            'schedule'              => $request->schedule_date,
            'month'                 => $request->schedule_month,
            'time'                  => $request->schedule_time,
            'yearly'                => $request->schedule_yearly_date,
            'start_date'            => $request->start_date,
            'end_date'              => $request->end_date,
            'broadcast_method'      => $request->broadcast_method,
            'ai_prompt'             => $request->broadcast_method == 'ai' ? $request->ai_prompt : null,
            'template_id'           =>  $request->broadcast_method == 'template' ?  $request->template : null,
            'labels'                => !empty($request->label) ? implode(",", $request->label) : null,
            'category_id'           => $request->category ?? null, 
        ]);
    }

    public function deleteData(BlashWhatsapp $blash)
    {
        $blash->details()->delete();
        $blash->delete();
        return redirect()->back()->with(['flash'    => 'Berhasil menghapus data']);
    }
}
