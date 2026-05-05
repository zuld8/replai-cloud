<?php

namespace App\Observers\Master;

use App\Models\Courier\Courier;
use Illuminate\Http\Request;

class CourierObserver
{
    public function getData(Request $request)
    {
        return Courier::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%')->orWhere('code', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Courier::create([
            'name'          => $request->name,
            'code'          => $request->code
        ]);
    }

    public function updateData(Request $request, Courier $courier)
    {
        $courier->update([
            'name'          => $request->name,
            'code'          => $request->code
        ]);
    }

    public function deleteData(Courier $courier)
    {
        $courier->delete();
    }
}
