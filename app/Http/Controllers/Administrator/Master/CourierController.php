<?php

namespace App\Http\Controllers\Administrator\Master;

use App\Http\Controllers\Controller;
use App\Models\Courier\Courier;
use App\Observers\Master\CourierObserver;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Master Couriers
    |--------------------------------------------------------------------------
    */

    protected $courierObserver;

    public function __construct(CourierObserver $courierObserver)
    {
        $this->courierObserver     = $courierObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Courier Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $couriers     = $this->courierObserver->getData($request)->get(['id', 'name', 'code', 'logo', 'status','service']);
        return view('admin.courier.index', ['page' => __('courier.courier_list'), 'breadcumb' => true], compact('couriers'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Courier Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.courier.create', ['page' => __('courier.add_courier'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Courier Page
    |--------------------------------------------------------------------------
    */

    public function update(Courier $courier)
    {
        return view('admin.courier.update', ['page' => __('courier.edit_courier'), 'breadcumb' => true], compact('courier'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Courier Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
        ]);

        $image = $request->logo ? $this->uploadImage($request, 'logo', 'courier') : '';
        $this->courierObserver->createData($request, $image);

        return redirect()->route('couriers')->with(['flash'    => 'Data berhasil di tambahkan']);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Courier Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Courier $courier)
    {
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
        ]);

        $image = $request->logo ? $this->uploadImage($request, 'logo', 'courier') : '';
        $image != '' ? $this->unlinkFile($courier->logo) : '';
        $this->courierObserver->updateData($request, $courier, $image);

        return redirect()->route('couriers')->with(['flash'    => 'Data berhasil di perbaharui']);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Courier Data
    |--------------------------------------------------------------------------
    */

    public function delete(Courier $courier)
    {
        $this->unlinkFile($courier->logo);
        $this->courierObserver->deleteData($courier);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }



    public function changeStatus(Courier $courier)
    {
        $courier->update([
            'status'        => $courier->status == 'yes' ? 'no' : 'yes'
        ]);

        return response()->json([
            'change_to' => $courier->status == 'yes' ? true : false,
            'message'   => __('general.success_update'),
        ]);
    }
}
