<?php

namespace App\Http\Controllers\Administrator\Blog;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use App\Observers\Saas\Blog\BlogCategoryObserver;
use App\Observers\Saas\Blog\BlogObserver;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Blog Controller
    |--------------------------------------------------------------------------
    */

    protected $blogObserver;
    protected $categoryObserver;

    public function __construct(BlogObserver $blogObserver, BlogCategoryObserver $categoryObserver)
    {
        $this->blogObserver     = $blogObserver;
        $this->categoryObserver = $categoryObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Blogs Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $blogs     = $this->blogObserver->getData($request)->get(['id', 'name', 'thumbnail', 'slug','category_id']);
        return view('admin.blog.index', ['page' => __('page.blog.page'), 'breadcumb' => true], compact('blogs'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Blog Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.blog.create', ['page' => __('page.blog.add'), 'breadcumb' => true], compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Blog Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Blog $blog)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.blog.update', ['page' => __('page.blog.edit'), 'breadcumb' => true], compact('blog', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Template Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'category'      => 'required', 
            'subject'       => 'required|string',
            'thumbnail'     => 'mimes:jpeg,jpg,png',  
            'content'       => 'required'
        ]);

        $image = $request->thumbnail ? $this->uploadImage($request, 'thumbnail', 'blogs') : '';
        $this->blogObserver->createData($request, $image);

        return redirect()->route('blogs')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Blog Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Blog $blog)
    {
        $this->validate($request, [
            'category'      => 'required', 
            'subject'       => 'required|string',
            'thumbnail'     => 'mimes:jpeg,jpg,png',  
            'content'       => 'required'
        ]);

        $image = $request->thumbnail ? $this->uploadImage($request, 'thumbnail', 'blogs') : '';
        $image != '' ? $this->unlinkFile($blog->thumbnail) : '';
        $this->blogObserver->updateData($request, $blog, $image);

        return redirect()->route('blogs')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Blog Data
    |--------------------------------------------------------------------------
    */

    public function delete(Blog $blog)
    {
        $this->unlinkFile($blog->thumbnail);
        $this->blogObserver->deleteData($blog);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
