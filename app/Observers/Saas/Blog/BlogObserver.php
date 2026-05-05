<?php

namespace App\Observers\Saas\Blog;

use App\Models\Cms\Blog;
use Illuminate\Http\Request;

class BlogObserver
{
    public function getData(Request $request)
    {
        return Blog::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->category ? $q->where('category_id', 'like', '%' . $request->category . '%') : '';
        })->orderBy('created_at', 'desc');
    }

    public function getBySlug(String $slug)
    {
        return Blog::where("slug", $slug)->first();
    }

    public function createData(Request $request, String $image)
    {
        return Blog::create([
            'category_id'           => $request->category,
            'name'                  => $request->subject,
            'thumbnail'             => $image,
            'keyword'               => $request->keyword,
            'meta_description'      => $request->description,
            'description'           => $request->content
        ]);
    }

    public function updateData(Request $request, Blog $blog, String $image)
    {

        $blog->update([
            'category_id'           => $request->category,
            'name'                  => $request->subject,
            'thumbnail'             => $image != '' ? $image : $blog->thumbnail,
            'keyword'               => $request->keyword,
            'meta_description'      => $request->description,
            'description'           => $request->content
        ]);
    }

    public function deleteData(Blog $blog)
    {
        $blog->delete();
    }
}
