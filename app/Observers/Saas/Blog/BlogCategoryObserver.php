<?php

namespace App\Observers\Saas\Blog;

use App\Models\Cms\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryObserver
{
    public function getData(Request $request)
    {
        return BlogCategory::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return BlogCategory::create([
            'name'      => $request->name
        ]);
    }

    public function updateData(Request $request, BlogCategory $category)
    {
        $category->update([
            'name'      => $request->name
        ]);
    }

    public function deleteData(BlogCategory $category)
    {
        $category->delete();
    }
}
