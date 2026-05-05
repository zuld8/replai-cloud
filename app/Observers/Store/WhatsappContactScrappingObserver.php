<?php

namespace App\Observers\Store;

use App\Models\Store\Scrapping;
use Illuminate\Http\Request;

class WhatsappContactScrappingObserver
{
    public function getData(Request $request)
    {
        return Scrapping::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->category ? $q->where("category_id", $request->category) : '';
        })->where(function ($q) use ($request) {
            return $request->district ? $q->where("district_id", $request->district) : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where('scrapping_method', 'contacts')->orderBy('created_at', 'desc');
    }

    public function createData(Request $request)
    {
        return Scrapping::create([ 
            'category_id'           => $request->category,
            'devices'               => implode(",", $request->devices),
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'scrapping_method'      => 'contacts'
        ]);
    }

    public function updateData(Request $request, Scrapping $scrapping)
    {
        $scrapping->update([ 
            'category_id'           => $request->category,
            'devices'               => implode(",", $request->devices),
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
            'scrapping_method'      => 'contacts'
        ]);
    }

    public function deleteData(Scrapping $scrapping)
    {
        $scrapping->delete();
    }
}
