<?php

namespace App\Observers\Saas;

use App\Models\Merchant\MerchantCategory;
use Illuminate\Http\Request;

class MerchantCategoryObserver
{
    public function getData(Request $request)
    {
        return MerchantCategory::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return MerchantCategory::create([
            'name'      => $request->name
        ]);
    }

    public function updateData(Request $request, MerchantCategory $category)
    {
        $category->update([
            'name'      => $request->name
        ]);
    }

    public function deleteData(MerchantCategory $category)
    {
        $category->delete();
    }
}
