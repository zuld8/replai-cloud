<?php

namespace App\Http\Controllers\Store;

use App\Exports\ScrappingResultExport;
use App\Http\Controllers\Controller;
use App\Models\Store\Scrapping;
use App\Models\Store\Store;
use App\Models\Store\WhatsappGroup;
use App\Models\WhatsappDevice;
use App\Observers\Master\CategoryObserver;
use App\Observers\Store\GroupScrappingObserver;
use App\Observers\Store\StoreObserver;
use App\Observers\Store\WhatsappGroupObserver;
use App\Observers\WhatsappDeviceObserver;
use App\Observers\WhatsappServiceObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class GroupScrappingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Scraping From Group Data
    |--------------------------------------------------------------------------
    */

    protected $scrappingObserver;
    protected $storeObserver;
    protected $whatsappDeviceObserver;
    protected $categoryObserver;
    protected $whatsappServiceObserver;
    protected $uploadImageProcess;
    protected $whatsappGroupObserver;

    public function __construct(GroupScrappingObserver $scrappingObserver, StoreObserver $storeObserver, WhatsappDeviceObserver $whatsappDeviceObserver, CategoryObserver $categoryObserver, WhatsappServiceObserver $whatsappServiceObserver, UploadImageProcess $uploadImageProcess, WhatsappGroupObserver $whatsappGroupObserver)
    {
        $this->whatsappDeviceObserver   = $whatsappDeviceObserver;
        $this->scrappingObserver        = $scrappingObserver;
        $this->storeObserver            = $storeObserver;
        $this->categoryObserver         = $categoryObserver;
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
        $this->uploadImageProcess       = $uploadImageProcess;
        $this->whatsappGroupObserver    = $whatsappGroupObserver;
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
                })->addColumn('group_total', function ($row) {
                    return $row->groups->count() . ' Group';
                })->addColumn('action', function ($row) {
                    $html = ' <a href="' . route('scrapping_group.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('scrapping_group.detail', $row->id) . '" class="btn btn-outline-info btn-icon fs-16 ">
                                <i class="bx bx-detail"></i>
                            </a>
                            <a href="' . route('scrapping_group.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'success' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['status_attribute', 'action', 'schedule_attribute'])
                ->make(true);
        }

        return view('scrapping_group.index', ['page'  => __('scraping.group.list_title'), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $devices        = $this->whatsappDeviceObserver->getData($request)->get(['id', 'name', 'phone']);
        return view('scrapping_group.create', ['page' => __('page.scrapp.add'), 'breadcumb' => true], compact('devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Scrapping $scrapping)
    {
        $devices        = $this->whatsappDeviceObserver->getData($request)->get(['id', 'name', 'phone']);
        return view('scrapping_group.update', ['page' => __('page.scrapp.update'), 'breadcumb' => true], compact('devices', 'scrapping'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'devices'       => 'required',
            'name'          => 'required',
            'schedule'      => 'required',
        ]);

        $this->scrappingObserver->createData($request);

        return redirect()->route('scrapping_group')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Scrapping $scrapping)
    {
        $this->validate($request, [
            'devices'       => 'required',
            'name'          => 'required',
            'schedule'      => 'required'
        ]);

        $this->scrappingObserver->updateData($request, $scrapping);

        return redirect()->route('scrapping_group')->with(['flash'    => __('general.success_update')]);
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

            $stores         = $this->whatsappGroupObserver->getData($request)->where("scrapping_id", $scrapping->id);

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
                            </a>';

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
        return view('scrapping_group.detail', ['page'  => __('page.scrapp.result'), 'breadcumb' => true], compact('scrapping'));
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


    /*
    |--------------------------------------------------------------------------
    | 10. Callback
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request, $id, $business)
    {

        $group  = WhatsappGroup::where('group_id', $id)->where('business_id', $business)->first();

        if ($group) {
            foreach ($request->members as $member) {
                $waId = $member['wa_id'];
                $name = $member['name'];

                $checkStore = Store::where("business_id", $group->business_id)->where('whatsapp_group_id', $group->id)->where('phone', $waId)->first();

                if (!$checkStore) {
                    Store::create([
                        'name'              => $name != '' ? $name : $request->group_name . ' - ' . $waId,
                        'phone'             => $waId,
                        'whatsapp_group_id' => $group->id,
                        'merchant_id'       => $group->merchant_id,
                        'business_id'       => $group->business_id,
                    ]);
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 11. Callback For Get Groups
    |--------------------------------------------------------------------------
    */

    public function groupCallback(Request $request, $id)
    {

        $scrapping  = Scrapping::find($id);
        $device_id  = str_replace('device_', '', $request->session);

        if ($scrapping) {

            $image  = '';

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/groups/');
            }

            $checkData  = WhatsappGroup::where('group_id', $request->group_id)->where('business_id', $scrapping->business_id)->first();

            if (!$checkData) {
                WhatsappGroup::create([
                    'name'              => $request->group_name,
                    'image'             => $image,
                    'scrapping_id'      => $scrapping->id,
                    'description'       => $request->group_description,
                    'merchant_id'       => $scrapping->merchant_id,
                    'business_id'       => $scrapping->business_id,
                    'group_id'          => $request->group_id,
                    'device_id'         => $device_id
                ]);
            } else {
                $checkData->update([
                    'device_id'         => $device_id
                ]);
            }
        }
    }
}
