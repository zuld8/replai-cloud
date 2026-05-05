<?php

namespace App\Http\Controllers\Blash;

use App\Exports\BlashGroupResultExport;
use App\Http\Controllers\Controller;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Setting;
use App\Models\Store\WhatsappGroup;
use App\Observers\Blash\BlashDetailObserver;
use App\Observers\Blash\BlashWhatsappObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappDeviceObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BlashWhatsappGroupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Blash Whastapp Controllers
    |--------------------------------------------------------------------------
    */

    protected $blashWhatsappObserver;
    protected $blashDetailObserver;
    protected $templateObserver;
    protected $whatsappDeviceObserver;

    public function __construct(
        BlashWhatsappObserver $blashWhatsappObserver,
        BlashDetailObserver $blashDetailObserver,
        TemplateObserver $templateObserver,
        WhatsappDeviceObserver $whatsappDeviceObserver,
    ) {
        $this->blashWhatsappObserver    = $blashWhatsappObserver;
        $this->blashDetailObserver      = $blashDetailObserver;
        $this->templateObserver         = $templateObserver;
        $this->whatsappDeviceObserver   = $whatsappDeviceObserver;
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

            $blashs         = $this->blashWhatsappObserver->getData($request, 'whatsapp_group')->where('meta_account_id', null);

            return DataTables::of($blashs)
                ->addColumn('schedule_attribute', function ($row) {
                    $date   = tanggal_indo(substr($row->schedule, 0, 10));
                    $time   = substr($row->schedule, 11, 16);
                    return $date . ' ' . $time;
                })->addColumn('template', function ($row) {
                    $html = '<a class="text-info" href="' . route('blash.group') . '?template=' . $row->template_id . '"> ' . ($row->template->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = ' <a href="' . route('blash.group.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('blash.group.detail', $row->id) . '" class="btn btn-outline-info btn-icon fs-16 ">
                                <i class="bx bx-detail"></i>
                            </a>
                            <a href="' . route('blash.group.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'success' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['status_attribute', 'action', 'schedule_attribute', 'template'])
                ->make(true);
        }
        return view('blash.group.index', ['page'  => __('page.wa.page'), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {

        $devices        = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $templates      = $this->templateObserver->getData($request)->where('type', 'whatsapp')->where('meta_account_id', null)->get(['id', 'name']);
        return view('blash.group.create', ['page' => __('page.wa.add'), 'breadcumb' => true], compact('templates', 'devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, BlashWhatsapp $blash)
    { 

        $devices        = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $templates      = $this->templateObserver->getData($request)->where("type", 'whatsapp')->where('meta_account_id', null)->get(['id', 'name']);
        return view('blash.group.update', ['page' => __('page.wa.update'), 'breadcumb' => true], compact('templates', 'devices', 'blash'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'schedule'          => 'required',
            'template'          => 'required',
            'devices'           => 'required'
        ]);


        $topupLimit     = 0;
        $packageCredit  = 0;
        $business       = Setting::find(my_business());

        if (($business->merchant ?? null) != null) {
            $topupLimit     = $business->package_active_topup->sisa_credit ?? 0;
            $packageCredit  = $business->package_active->sisa_credit ?? 0;
            $totalCredit    = ($topupLimit + $packageCredit);

            $stores         = WhatsappGroup::where(function ($q) use ($request) {
                $q->whereIn('device_id', $request->devices);
            })->orderBy('name', 'asc')->count();

            if ($totalCredit < $stores) {
                return redirect()->back()->with(['gagal'    => 'Maaf, Kredit limit anda tidak mencukupi, silahkan isi ulang saldo kredit anda']);
            }
        }

        $broadcast   = $this->blashWhatsappObserver->createData($request, 'whatsapp_group');
        $groups      = WhatsappGroup::whereIn('id', $request->groups)
            ->whereIn('device_id', $request->devices)
            ->pluck('id');

        $broadcast->update([
            "groups"        => $groups->implode(',')
        ]);

        return redirect()->route('blash.group')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, BlashWhatsapp $blash)
    {
        $this->validate($request, [
            'name'              => 'required',
            'schedule'          => 'required',
            'template'          => 'required',
            'devices'           => 'required'
        ]);

        $topupLimit     = 0;
        $packageCredit  = 0;
        $business       = $blash->business;

        if (($business->merchant ?? null) != null) {
            $topupLimit     = $business->package_active_topup->sisa_credit ?? 0;
            $packageCredit  = $business->package_active->sisa_credit ?? 0;
            $totalCredit    = ($topupLimit + $packageCredit);

            $stores         = WhatsappGroup::where(function ($q) use ($request) {
                $q->whereIn('device_id', $request->devices);
            })->orderBy('name', 'asc')->count();

            if ($totalCredit < $stores) {
                return redirect()->back()->with(['gagal'    => 'Maaf, Kredit limit anda tidak mencukupi, silahkan isi ulang saldo kredit anda']);
            }
        }

        $this->blashWhatsappObserver->updateData($request, $blash);

        $groups      = WhatsappGroup::whereIn('id', $request->groups)
            ->whereIn('device_id', $request->devices)
            ->pluck('id');

        $blash->update([
            "groups"        => $groups->implode(',')
        ]);

        return redirect()->route('blash.group')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(BlashWhatsapp $blash)
    {
        $this->blashWhatsappObserver->deleteData($blash);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
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
                    return $row->group->name ?? '';
                })
                ->addColumn('date', function ($row) {
                    return tanggal_indo(substr($row->created_at, 0, 10)) . ' ' . substr($row->created_at, 11, 16);
                })->addColumn('jadwal', function ($row) {
                    return tanggal_indo(substr($row->schedule, 0, 10)) . ' ' . substr($row->schedule, 11, 16);
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'yes' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['store',  'date', 'status_attribute'])
                ->make(true);
        }

        return view('blash.group.detail', ['page'  => __('page.wa.detail'), 'breadcumb' => true], compact('blash'));
    }



    /*
    |--------------------------------------------------------------------------
    | 10. Export Data
    |--------------------------------------------------------------------------
    */

    public function export(Request $request, BlashWhatsapp $blash)
    {
        return (new BlashGroupResultExport($request, $this->blashDetailObserver, $blash))->download('blash_result-' . $blash->id . '.xlsx');
    }
}
