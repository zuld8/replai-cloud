<?php

namespace App\Observers\Store;

use App\Models\ChatBot\HistoryChat;
use App\Models\Store\Store;
use Illuminate\Http\Request;

class StoreObserver
{
    public function getData(Request $request)
    {
  return Store::with([
        'district.city.province',
        'category',
        'label',
        'history' => function($q) {
            $q->with([
                'livechat.finetunnel',
                'device.finetunnel',
                'handled',
                'resolved'
            ]);
        }
    ])->when($request->name, fn($q) => $q->where('name', 'like', $request->name . '%'))
      ->when($request->category, fn($q) => $q->where('category_id', $request->category))
      ->when($request->district, fn($q) => $q->where('district_id', $request->district))
      ->when($request->status, fn($q) => $q->where('status', $request->status))
      ->when($request->meta_account_id, function($q) use ($request) {
          // Strip waba_/personal_ prefix — DB stores UUID only
          $metaId = preg_replace('/^(waba_|personal_)/', '', $request->meta_account_id);
          return $q->where('meta_account_id', $metaId);
      })
      ->orderBy('name', 'asc');
    }

    public function createData(Request $request)
    {
        return Store::create([
            'category_id'           => $request->category,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'phone'                 => $request->phone,
            'email'                 => $request->email,
            'label_id'              => $request->label ?? null,
            'meta_account_id'       => $request->meta_account_id ?? null,
            'address'               => $request->address,
        ]);
    }

    public function updateData(Request $request, Store $store)
    {
        $store->update([
            'category_id'           => $request->category,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'phone'                 => $request->phone,
            'email'                 => $request->email,
            'address'               => $request->address,
            'label_id'              => $request->label ?? null,
            'status'                => $request->status
        ]);
    }

    public function checkByNumber($number, $businessId)
    {
        // BSUID Support: jika from_number null (user pakai BSUID), skip lookup
        if (empty($number)) {
            return null;
        }
        return Store::where('business_id', $businessId)->where('phone', $number)->first(['id']);
    }

    public function checkByNumberAndJid($businessId, $number = null, $jidNumber = null)
    {
        return Store::where('business_id', $businessId)->where(function ($q) use ($number, $jidNumber) {
            return $q->where('phone', $number)->orWhere(function ($q) use ($jidNumber) {
                return $jidNumber ? $q->where('jid_number', $jidNumber) : '';
            });
        })->first(['id']);
    }

    public function createByHistory(HistoryChat $history)
    {
        return Store::create([
            'name'              => $history->name,
            'phone'             => $history->from_number,
            'business_id'       => $history->business_id,
            'merchant_id'       => $history->merchant_id,
            'jid_number'        => $history->jid_number,
            'metadata'          => $history->metadata
        ]);
    }

    public function deleteData(Store $store)
    {
        $store->delete();
    }
}
