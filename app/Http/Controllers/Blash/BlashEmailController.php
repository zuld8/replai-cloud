<?php

namespace App\Http\Controllers\Blash;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashWhatsapp;
use App\Observers\Blash\BlashDetailObserver;
use App\Observers\Blash\BlashWhatsappObserver;
use App\Observers\Master\CategoryObserver;
use App\Observers\Master\DirectoryObserver;
use App\Observers\Master\TemplateObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BlashEmailController extends Controller
{



    /*
    |--------------------------------------------------------------------------
    | Blash Email Controllers
    |--------------------------------------------------------------------------
    */

    protected $blashWhatsappObserver;
    protected $directoryObserver;
    protected $categoryObserver;
    protected $blashDetailObserver;
    protected $templateObserver;

    public function __construct(
        BlashWhatsappObserver $blashWhatsappObserver,
        DirectoryObserver $directoryObserver,
        CategoryObserver $categoryObserver,
        BlashDetailObserver $blashDetailObserver,
        TemplateObserver $templateObserver
    ) {
        $this->blashWhatsappObserver    = $blashWhatsappObserver;
        $this->directoryObserver        = $directoryObserver;
        $this->categoryObserver         = $categoryObserver;
        $this->blashDetailObserver      = $blashDetailObserver;
        $this->templateObserver         = $templateObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Blash List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $blashs         = $this->blashWhatsappObserver->getData($request, 'email');

            return DataTables::of($blashs)
                ->addColumn('schedule_attribute', function ($row) {
                    $date   = tanggal_indo(substr($row->schedule, 0, 10));
                    $time   = substr($row->schedule, 11, 16);
                    return $date . ' ' . $time;
                })->addColumn('template', function ($row) {
                    $html = '<a class="text-info" href="' . route('blash_email') . '?template=' . $row->template_id . '"> ' . ($row->template->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('province', function ($row) {
                    $province   = $row->city->province->name ?? '';
                    $provinceID = $row->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->city->type ?? '';
                    $cityName = $row->city->name ?? '';
                    $html = '<a href="' . route('blash_email') . '?city=' . ($row->city_id ?? '') . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('category', function ($row) {
                    $html = '<a class="text-info" href="' . route('blash_email') . '?category=' . $row->category_id . '"> ' . ($row->category->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('district', function ($row) {
                    $html = '<a class="text-info" href="' . route('blash_email') . '?district=' . $row->district_id . '"> ' . ($row->district->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = ' <a href="' . route('blash_email.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('blash_email.detail', $row->id) . '" class="btn btn-outline-info btn-icon fs-16 ">
                                <i class="bx bx-detail"></i>
                            </a>
                            <a href="' . route('blash.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'success' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['province',  'city', 'category', 'district', 'status_attribute', 'action', 'schedule_attribute', 'template'])
                ->make(true);
        }
        return view('email.index', ['page'  => __('page.email.page'), 'breadcumb' => true], compact('params'));
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
        $templates      = $this->templateObserver->getData($request)->where("type", "email")->where('for_waba', 'no')->get(['id', 'name']);
        return view('email.create', ['page' => __('page.email.add'), 'breadcumb' => true], compact('categories', 'provinces', 'templates'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, BlashWhatsapp $blash)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        $templates      = $this->templateObserver->getData($request)->where("type", "email")->where('for_waba', 'no')->get(['id', 'name']);
        return view('email.update', ['page' => __('page.email.update'), 'breadcumb' => true], compact('categories', 'provinces', 'blash', 'templates'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'category'          => 'required', 
            'name'              => 'required',
            'schedule'          => 'required',
            'template'          => 'required'
        ]);

        $this->blashWhatsappObserver->createData($request, 'email');

        return redirect()->route('blash_email')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, BlashWhatsapp $blash)
    {
        $this->validate($request, [
            'category'          => 'required', 
            'name'              => 'required',
            'schedule'          => 'required',
            'template'          => 'required'
        ]);

        $this->blashWhatsappObserver->updateData($request, $blash);

        return redirect()->route('blash_email')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Details List
    |--------------------------------------------------------------------------
    */

    public function detail(Request $request, BlashWhatsapp $blash)
    {
        if ($request->ajax()) {

            $blashs         = $this->blashDetailObserver->getData($request, $blash);

            return DataTables::of($blashs)
                ->addColumn('store', function ($row) {
                    return $row->store->name ?? '';
                })
                ->addColumn('date', function ($row) {
                    return tanggal_indo(substr($row->created_at, 0, 10)) . ' ' . substr($row->created_at, 11, 16);
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'yes' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['store',  'date', 'status_attribute'])
                ->make(true);
        }

        return view('email.detail', ['page'  => __('page.email.detail'), 'breadcumb' => true], compact('blash'));
    }
}
