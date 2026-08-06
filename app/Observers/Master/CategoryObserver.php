<?php

namespace App\Observers\Master;

use App\Models\Master\Category;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryObserver
{
    public function getData(Request $request)
    {
        return Category::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Category::create([
            'name'      => $request->name
        ]);
    }

    public function updateData(Request $request, Category $category)
    {
        $category->update([
            'name'      => $request->name
        ]);
    }

    public function deleteData(Category $category)
    {
        DB::transaction(function () use ($category) {
            // Hapus semua kontak (stores) di dalam kategori ini secara permanen.
            // User sudah konfirmasi via dialog peringatan di blade.
            Store::where('category_id', $category->id)->delete();

            // Hapus kategori
            $category->delete();
        });
    }
}
