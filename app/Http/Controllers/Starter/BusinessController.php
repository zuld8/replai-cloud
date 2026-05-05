<?php

namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Observers\Saas\BusinessObserver;
use App\Observers\Saas\PricingObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    protected $businessObserver;
    protected $pricingObserver;

    public function __construct(BusinessObserver $businessObserver, PricingObserver $pricingObserver)
    {
        $this->businessObserver     = $businessObserver;
        $this->pricingObserver      = $pricingObserver;
    }

    public function index(Request $request)
    {
        $businesses     = $this->businessObserver->getData($request)->get(['id', 'name', 'created_at']);
        return view('starter.business.index', ['page'   => 'Daftar Bisnis'], compact('businesses'));
    }

    public function create(Request $request)
    {
        return view('starter.business.create', ['page'  => 'Tambah Bisnis']);
    }

    public function choosedBusiness($business)
    {
        $userBusiness = Setting::where('id', $business)->where(function ($q) {
            $storeData = explode(",", my_user()->business_id);
            return $q->whereIn('id', $storeData);
        })->first(['id']);

        if (!$userBusiness) {
            return redirect()->back()->with(['gagal'    => 'Anda tidak memiliki akses untuk masuk bisnis ini']);
        }

        session()->put('businessid', $business);
        return redirect()->route('index');
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'                      => 'required|string',
            'timezone'                  => 'required',
            'phone_country_code'        => 'required',
            'default_lang'              => 'required',
            'api_device_use'            => 'required'
        ]);



        try {

            DB::beginTransaction();

            $business   = $this->businessObserver->createData($request);
            $owner      = $business->merchant->owner ?? null;
            $user       = my_user();
            if ($owner) {
                $owner->update([
                    'business_id'   => $owner->business_id == null ? $business->id : $owner->business_id . ',' . $business->id
                ]);
            }

            if ($user->id != ($owner ? $owner->id : null)) {
                $user->update([
                    'business_id'   => $user->business_id == null ? $business->id : $user->business_id . ',' . $business->id
                ]);
            }

            DB::commit();

            return redirect()->route('starter.business.index')->with(['flash'   => __('general.success_add_data')]);
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }

    public function detail(Request $request, Setting $business)
    {
        $packages   = $this->pricingObserver->getData($request)->get();
        return view('starter.business.detail', ['page'  => 'Pembelian Paket Bisnis'], compact('business', 'packages'));
    }

    public function deleteBusiness(Setting $business)
    {

        $userBusiness = Setting::where('id', $business->id)->where(function ($q) {
            $storeData = explode(",", my_user()->business_id);
            return $q->whereIn('id', $storeData);
        })->first(['id']);

        if (!$userBusiness) {
            return redirect()->back()->with(['gagal'    => 'Anda tidak memiliki akses untuk masuk bisnis ini']);
        }

        if ($business->package_active) {
            return redirect()->back()->with(['gagal'    => 'bisnis ini tidak dapat di hapus apabila masih memiliki paket aktif']);
        }

        try {

            DB::beginTransaction();

            $business->delete();

            DB::commit();

            return redirect()->route('starter.business.index')->with(['flash'   => 'Data bisnis berhasil di hapus']);
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }
}
