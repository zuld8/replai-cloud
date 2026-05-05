<?php

namespace App\Observers\Saas;

use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantCategory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class MerchantObserver
{

    public function getData(Request $request)
    {
        return Merchant::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->orderBy('name', 'asc');
    }

    public function businessCategories(Request $request)
    {
        return MerchantCategory::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function deleting(Merchant $merchant)
    {
        User::withoutGlobalScopes()->where('merchant_id', $merchant->id)->forceDelete();
        Setting::withoutGlobalScopes()->where('merchant_id', $merchant->id);
    }
}
