<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\WhatsappGroup;
use App\Observers\Store\StoreObserver;
use App\Observers\Store\WhatsappGroupObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WhatsappGroupScrappingController extends Controller
{
    protected $whatsappGroupObserver;
    protected $storeObserver;

    public function __construct(WhatsappGroupObserver $whatsappGroupObserver, StoreObserver $storeObserver)
    {
        $this->whatsappGroupObserver        = $whatsappGroupObserver;
        $this->storeObserver                = $storeObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Group List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $stores         = $this->whatsappGroupObserver->getData($request);

            return DataTables::of($stores)
                ->addColumn('contact_total', function ($row) {
                    return $row->contacts->count() . ' Kontak';
                })->addColumn('detail', function ($row) {
                    $html = '<div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-2 avatar-rounded lh-1">
                                        <img src="' . asset($row->image) . '" alt="avatar">
                                    </div>
                                    <div class="lh-1">
                                        <a href="#">' . $row->name . '</a>
                                        <p class="text-muted fs-11 mb-0 mt-1">' . $row->description . '</p>
                                    </div>
                                </div>';

                    return $html;
                })->addColumn('device', function ($row) {
                    $devicePhone    = $row->device->phone ?? '';
                    $deviceName     = $row->device->name ?? '';
                    return $deviceName . '(' . $devicePhone . ')';
                })->addColumn('action', function ($row) {
                    $html = '
                    <a href="' . route('group.detail', $row->id) . '" class="btn btn-outline-info btn-icon fs-16">
                                <i class="bx bx-list-ul "></i>
                            </a>
                            <a href="' . route('group.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>
                            ';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->scraping == 'no' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['detail', 'status_attribute', 'action'])
                ->make(true);
        }
        return view('groups.index', ['page'  => __('scraping.group.whatsapp_group'), 'breadcumb' => true]);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(WhatsappGroup $group)
    {
        $group->update([
            'scraping'        => $group->scraping == 'yes' ? 'no' : 'yes'
        ]);

        return response()->json([
            'message'  => __('general.success_update'),
        ]);
    }


    /*
    |-------------------------------------------------------------------------
    | 3. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(WhatsappGroup $group)
    {
        $group->delete();
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Detail Result
    |--------------------------------------------------------------------------
    */

    public function detail(Request $request, WhatsappGroup $group)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $stores         = $this->storeObserver->getData($request)->where('whatsapp_group_id', $group->id);

            return DataTables::of($stores)
                ->addColumn('identity', function ($row) {
                    return '<div class="d-flex align-items-center"> 
                                <div class="d-flex flex-column">
                                  <span class="fw-semibold lh-1">' . $row->name . '</span>
                                  <small class="text-muted">' . $row->email . '</small>
                                </div>
                              </div>';
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('stores.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('stores.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->rawColumns(['action',  'identity'])
                ->make(true);
        }

        return view('groups.detail', ['page'  => 'Detail Kontak Group ' . $group->name, 'breadcumb' => true], compact('params', 'group'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Components
    |--------------------------------------------------------------------------
    */
    public function components(Request $request)
    {
        $groups     = $this->whatsappGroupObserver->getData($request)->get(['id', 'name']);
        return response()->json($groups);
    }
}
