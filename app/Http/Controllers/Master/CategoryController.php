<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Category;
use App\Observers\Master\CategoryObserver;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Master Categories
    |--------------------------------------------------------------------------
    */

    protected $categoryObserver;

    public function __construct(CategoryObserver $categoryObserver)
    {
        $this->categoryObserver     = $categoryObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Category Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)
                            ->select('id', 'name')
                            ->withCount('store')
                            ->get();
        return view('master.category.index', ['page' => __('page.category.page'), 'breadcumb' => true], compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Category Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('master.category.create', ['page' => __('page.category.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Category Page
    |--------------------------------------------------------------------------
    */

    public function update(Category $category)
    {
        return view('master.category.update', ['page' => __('page.category.update'), 'breadcumb' => true], compact('category'));
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

        return redirect()->route('categories')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Category Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Category $category)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->categoryObserver->updateData($request, $category);

        return redirect()->route('categories')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Kategori Data
    |--------------------------------------------------------------------------
    */

    public function delete(Category $category)
    {
        $this->categoryObserver->deleteData($category);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
