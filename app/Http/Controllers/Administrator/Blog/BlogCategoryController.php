<?php

namespace App\Http\Controllers\Administrator\Blog;

use App\Http\Controllers\Controller;
use App\Models\Cms\BlogCategory;
use App\Observers\Saas\Blog\BlogCategoryObserver;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Blog Categories
    |--------------------------------------------------------------------------
    */

    protected $categoryObserver;

    public function __construct(BlogCategoryObserver $categoryObserver)
    {
        $this->categoryObserver     = $categoryObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Category 
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.blog.category.index', ['page' => __('page.blog_category.page'), 'breadcumb' => true], compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Category 
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.blog.category.create', ['page' => __('page.blog_category.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Category 
    |--------------------------------------------------------------------------
    */

    public function update(BlogCategory $category)
    {
        return view('admin.blog.category.update', ['page' => __('page.blog_category.edit'), 'breadcumb' => true], compact('category'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Category Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->categoryObserver->createData($request);

        return redirect()->route('blog.categories')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Category 
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, BlogCategory $category)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->categoryObserver->updateData($request, $category);

        return redirect()->route('blog.categories')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Category 
    |--------------------------------------------------------------------------
    */

    public function delete(BlogCategory $category)
    {
        $this->categoryObserver->deleteData($category);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
