<?php

namespace App\Observers\Saas;

use App\Models\Master\Bank;
use Illuminate\Http\Request;

class BankObserver
{
    public function getData(Request $request)
    {
        return Bank::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function createData(Request $request, String $image)
    {
        return Bank::create([
            'name'              => $request->name, 
            'code'              => $request->code,
            'number'            => $request->number,
            'logo'              => $image
        ]);
    }

    public function updateData(Request $request, Bank $bank, String $image)
    {
        $bank->update([
            'name'              => $request->name, 
            'code'              => $request->code,
            'number'            => $request->number,
            'logo'              => $image != '' ? $image : $bank->logo
        ]);
    }


    public function deleteData(Bank $bank)
    {
        $bank->delete();
    }
}
