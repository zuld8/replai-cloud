<?php

namespace App\Observers\Blash;

use App\Models\Blash\BlashDetail;
use App\Models\Blash\BlashWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlashWhatsappObserver
{
    public function getData(Request $request, String $type = 'whatsapp')
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
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where("use", $type) : '';
        })->where('waba', 'no')->orderBy('created_at', 'desc');
    }

    public function createData(Request $request, String $type = 'whatsapp')
    {
        return BlashWhatsapp::create([
            'category_id'           => $request->category,
            'city_id'               => $request->city,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'use'                   => $type,
            'template_id'           => $request->template,
            'delay'                 => $request->delay ?? 60,
            'devices'               => $request->devices ? implode(",", $request->devices) : null,
            'whatsapp_sender_notif' => $request->whatsapp_sender_notif ?? 'random',
            'group_whatsapp_id'     => $request->group,
            'stop_sending'          => $request->stop_sending ?? 0,
            'rest_sending'          => $request->rest_sending ?? 0
        ]);
    }

    public function updateData(Request $request, BlashWhatsapp $blash)
    {
        $blash->update([
            'category_id'           => $request->category,
            'city_id'               => $request->city,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'template_id'           => $request->template,
            'delay'                 => $request->delay ?? 60,
            'devices'               => $request->devices ? implode(",", $request->devices) : null,
            'whatsapp_sender_notif' => $request->whatsapp_sender_notif ?? 'random',
            'group_whatsapp_id'     => $request->group,
            'stop_sending'          => $request->stop_sending ?? 0,
            'rest_sending'          => $request->rest_sending ?? 0
        ]);
    }

    public function deleteData(BlashWhatsapp $blash)
    {
        $blash->details()->delete();
        $blash->delete();
        return redirect()->back()->with(['flash'    => 'Berhasil menghapus data']);
    }


    public function getStatisticData(Request $request)
    {
        return BlashDetail::select(
            'device_id',
            DB::raw("COUNT(*) as sent"),
            DB::raw("SUM(CASE WHEN sending_status = 'yes' THEN 1 ELSE 0 END) as delivered"),
            DB::raw("SUM(CASE WHEN sending_status = 'no' THEN 1 ELSE 0 END) as not_delivered"),
            DB::raw("
                ROUND(
                    (SUM(CASE WHEN sending_status = 'yes' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2
                ) as percent")
        )->whereHas('device', function ($q) {
            return $q->where('business_id', my_business());
        })->where(function ($q) use ($request) {
            if ($request->start_date && $request->end_date) {
                return $q->whereBetween('schedule', [$request->start_date, $request->end_date]);
            } else if ($request->start_date && $request->end_date == null) {
                return $q->where('schedule', $request->start_date);
            }
        })->groupBy('device_id')->get();
    }

    public function deleting(BlashWhatsapp $blash)
    {
        BlashDetail::withoutGlobalScopes()->where('blash_whatsapp_id', $blash->id)->delete();
    }
}
