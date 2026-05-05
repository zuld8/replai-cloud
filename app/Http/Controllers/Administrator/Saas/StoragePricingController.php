<?php

namespace App\Http\Controllers\Administrator\Saas;

use App\Http\Controllers\Controller;
use App\Models\Package\Package;
use App\Observers\Saas\PricingObserver;
use Illuminate\Http\Request;

class StoragePricingController extends Controller
{
    protected $pricingObserver;

    public function __construct(PricingObserver $pricingObserver)
    {
        $this->pricingObserver      = $pricingObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Category Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $packages     = $this->pricingObserver->getData($request, 'storage')->get();
        return view('admin.package.storage.index', ['page' => __('package.storage_package'), 'breadcumb' => true], compact('packages'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Package Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.package.storage.create', ['page' => __('page.package.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Package Page
    |--------------------------------------------------------------------------
    */

    public function update(Package $package)
    {
        return view('admin.package.storage.update', ['page' => __('page.package.edit'), 'breadcumb' => true], compact('package'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Package Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if ((int)$request->percentase_affiliate > (int)$request->price || $request->percentase_affiliate > 0 && $request->trial_version == 'yes') {
            return redirect()->back()->withInput()->with(['gagal'    => 'Komisi Affiliate tidak boleh lebih besar di bandingkan harga penjualan paket']);
        }

        $this->validate($request, [
            'name'                          => 'required|string',
            'price'                         => 'required',
            'days_option'                   => 'required|in:limited,unlimited',
            'add_days'                      => 'required_if:days_option,limited|numeric',
            'storage'                       => 'required|numeric|min:1'
        ]);

        $package = $this->pricingObserver->createStorage($request);

        return redirect()->route('package.storage')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Package Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Package $package)
    {

        if ((int)$request->percentase_affiliate > (int)$request->price || $request->percentase_affiliate > 0 && $request->trial_version == 'yes') {
            return redirect()->back()->withInput()->with(['gagal'    => 'Komisi Affiliate tidak boleh lebih besar di bandingkan harga penjualan paket']);
        }

        $this->validate($request, [
            'name'                          => 'required|string',
            'price'                         => 'required',
            'days_option'                   => 'required|in:limited,unlimited',
            'add_days'                      => 'required_if:days_option,limited|numeric',
            'storage'                       => 'required|numeric|min:1'
        ]);

        $this->pricingObserver->updateStorage($request, $package);

        return redirect()->route('package.storage')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Package Data
    |--------------------------------------------------------------------------
    */

    public function delete(Package $package)
    {
        $this->pricingObserver->deleteData($package);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
