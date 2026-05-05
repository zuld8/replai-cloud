<?php

namespace App\Http\Controllers\Administrator\Saas;

use App\Http\Controllers\Controller;
use App\Models\Merchant\MerchantCategory;
use App\Observers\Saas\MerchantCategoryObserver;
use Illuminate\Http\Request;

class MerchantCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Merchant Categories
    |--------------------------------------------------------------------------
    */

    protected $categoryObserver;

    public function __construct(MerchantCategoryObserver $categoryObserver)
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
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.merchants.category.index', ['page' => __('page.customer_category.page'), 'breadcumb' => true], compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Category Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.merchants.category.create', ['page' => __('page.customer_category.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Category Page
    |--------------------------------------------------------------------------
    */

    public function update(MerchantCategory $category)
    {
        return view('admin.merchants.category.update', ['page' => __('page.customer_category.update'), 'breadcumb' => true], compact('category'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Merchant Category Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->categoryObserver->createData($request);

        return redirect()->route('merchant.categories')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Merchant Category Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, MerchantCategory $category)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->categoryObserver->updateData($request, $category);

        return redirect()->route('merchant.categories')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Merchant Category Data
    |--------------------------------------------------------------------------
    */

    public function delete(MerchantCategory $category)
    {
        $this->categoryObserver->deleteData($category);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
