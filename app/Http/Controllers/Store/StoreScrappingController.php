<?php

namespace App\Http\Controllers\Store;

use App\Exports\ScrappingResultExport;
use App\Http\Controllers\Controller;
use App\Models\Store\Scrapping;
use App\Observers\Master\CategoryObserver;
use App\Observers\Master\DirectoryObserver;
use App\Observers\Store\StoreObserver;
use App\Observers\Store\StoreScrappingObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StoreScrappingController extends Controller
{



    /*
    |--------------------------------------------------------------------------
    | Store or Customer Data
    |--------------------------------------------------------------------------
    */

    protected $scrappingObserver;
    protected $directoryObserver;
    protected $categoryObserver;
    protected $storeObserver;

    public function __construct(StoreScrappingObserver $scrappingObserver, DirectoryObserver $directoryObserver, CategoryObserver $categoryObserver, StoreObserver $storeObserver)
    {
        $this->directoryObserver    = $directoryObserver;
        $this->categoryObserver     = $categoryObserver;
        $this->scrappingObserver    = $scrappingObserver;
        $this->storeObserver        = $storeObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Scrapp List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $scrappings         = $this->scrappingObserver->getData($request);

            return DataTables::of($scrappings)
                ->addColumn('schedule_attribute', function ($row) {
                    $date   = tanggal_indo(substr($row->schedule, 0, 10));
                    $time   = substr($row->schedule, 11, 16);
                    return $date . ' ' . $time;
                })
                ->addColumn('province', function ($row) {
                    $province   = $row->district->city->province->name ?? '';
                    $provinceID = $row->district->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->district->city->type ?? '';
                    $cityName = $row->district->city->name ?? '';
                    $html = '<a href="' . route('directory.districts') . '?city=' . ($row->district->city_id ?? '') . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('category', function ($row) {
                    $html = '<a class="text-info" href="' . route('scrappings') . '?category=' . $row->category_id . '"> ' . ($row->category->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('district', function ($row) {
                    $html = '<a class="text-info" href="' . route('scrappings') . '?district=' . $row->district_id . '"> ' . ($row->district->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = ' <a href="' . route('scrappings.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('scrappings.detail', $row->id) . '" class="btn btn-outline-info btn-icon fs-16 ">
                                <i class="bx bx-detail"></i>
                            </a>
                            <a href="' . route('scrappings.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'success' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['province',  'city', 'category', 'district', 'status_attribute', 'action', 'schedule_attribute'])
                ->make(true);
        }

        return view('scrappings.index', ['page'  => __('page.scrapp.page'), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('scrappings.create', ['page' => __('page.scrapp.add'), 'breadcumb' => true], compact('categories', 'provinces'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Scrapping $scrapping)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('scrappings.update', ['page' => __('page.scrapp.update'), 'breadcumb' => true], compact('categories', 'provinces', 'scrapping'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'category'      => 'required',
            'name'          => 'required',
            'schedule'      => 'required',
        ]);

        $this->scrappingObserver->createData($request);

        return redirect()->route('scrappings')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Scrapping $scrapping)
    {
        $this->validate($request, [
            'category'      => 'required',
            'name'          => 'required',
            'schedule'      => 'required',
        ]);

        $this->scrappingObserver->updateData($request, $scrapping);

        return redirect()->route('scrappings')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(Scrapping $scrapping)
    {
        $this->scrappingObserver->deleteData($scrapping);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Details List
    |--------------------------------------------------------------------------
    */

    public function detail(Request $request, Scrapping $scrapping)
    {
        if ($request->ajax()) {

            $stores         = $this->storeObserver->getData($request)->where("scrapping_id", $scrapping->id);

            return DataTables::of($stores)
                ->addColumn('province', function ($row) {
                    $province   = $row->district->city->province->name ?? '';
                    $provinceID = $row->district->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->district->city->type ?? '';
                    $cityName = $row->district->city->name ?? '';
                    $html = '<a href="' . route('directory.districts') . '?city=' . ($row->district->city_id ?? '') . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('category', function ($row) {
                    $html = '<a class="text-info" href="' . route('stores') . '?category=' . $row->category_id . '"> ' . ($row->category->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('district', function ($row) {
                    $html = '<a class="text-info" href="' . route('stores') . '?district=' . $row->district_id . '"> ' . ($row->district->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('stores.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('stores.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'yes' ? __('general.no_active') : __('general.active');
                    return $status;
                })->rawColumns(['province',  'city', 'category', 'district', 'status_attribute', 'action'])
                ->make(true);
        }
        return view('scrappings.detail', ['page'  => __('page.scrapp.result'), 'breadcumb' => true], compact('scrapping'));
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(Scrapping $scrapping)
    {
        $scrapping->update([
            'status'        => $scrapping->status == 'pending' ? 'success' : 'pending'
        ]);

        return response()->json([
            'message'  => __('general.success_update'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 9. Export Data
    |--------------------------------------------------------------------------
    */

    public function export(Request $request, Scrapping $scrapping)
    {
        return (new ScrappingResultExport($request, $this->storeObserver, $scrapping))->download('scrapping_result-' . $scrapping->id . '.xlsx');
    }
}
