<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\Directory\CityImport;
use App\Imports\Directory\DistrictImport;
use App\Imports\Directory\StateImport;
use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\Province;
use App\Observers\Master\DirectoryObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DirectoryController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Master Directory Guide
    |--------------------------------------------------------------------------
    */

    protected $directoryObserver;

    public function __construct(DirectoryObserver $directoryObserver)
    {
        $this->directoryObserver        = $directoryObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. State 
    |--------------------------------------------------------------------------
    */

    public function provinces(Request $request)
    {
        $provinces  = $this->directoryObserver->getProvince($request)->get(['id', 'name', 'status']);

        return view('master.directory.province.index', ['page' => __('page.state.page'), 'breadcumb' => true], compact('provinces'));
    }

    public function createProvince()
    {
        return view('master.directory.province.create', ['page'  => __('page.state.add'), 'breadcumb' => true]);
    }

    public function updateProvince(Province $province)
    {
        return view('master.directory.province.update', ['page'  => __('page.state.update'), 'breadcumb' => true], compact('province'));
    }

    public function deleteProvince(Province $province)
    {
        $province->delete();
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    public function storeProvince(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->directoryObserver->createProvince($request);
        return redirect()->route('directory.provinces')->with(['flash'  => __('general.success_add_data')]);
    }

    public function editProvince(Request $request, Province $province)
    {
        $this->validate($request, [
            'name'      => 'required'
        ]);

        $this->directoryObserver->updateProvince($request, $province);
        return redirect()->route('directory.provinces')->with(['flash'  => __('general.success_update')]);
    }

    public function importProvince(Request $request)
    {
        $this->validate($request, [
            'file'  => 'mimes:csv'
        ]);

        if ($request->file) {

            $import = Excel::toArray(new StateImport(), $request->file('file'));

            if (count($import[0]) > 0) {

                try {

                    DB::beginTransaction();

                    foreach ($import[0] as $d) {
                        if ($d['nm'] != null) {
                            Province::firstOrNew(
                                ['id'     =>  $d['id']],
                                [
                                    'name'  => $d['nm'],
                                    'image' => 'uploads/image.jpg'
                                ]
                            )->save();
                        }
                    }

                    DB::commit();

                    return redirect()->back()->with(['flash'    => __('general.success_import')]);
                } catch (\Exception $e) {

                    DB::rollBack();

                    return redirect()->back()->with(['gagal'    => $e->getMessage()]);
                }
            } else {
                return redirect()->back()->with(['gagal'    => __('general.file_not_reader')]);
            }
        }
    }


    public function changeProvinceStatus(Province $province)
    {
        $province->update([
            'status'        => $province->status == 'active' ? 'no' : 'active'
        ]);

        return response()->json([
            'change_to' => $province->status == 'active' ? true : false,
            'message'   => __('general.success_update'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Cities
    |--------------------------------------------------------------------------
    */

    public function cities(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $cities  = $this->directoryObserver->getCity($request);

            return DataTables::of($cities)
                ->editColumn('name', function ($row) {
                    return  $row->type . ' ' . $row->name;
                })->addColumn('province', function ($row) {
                    $province = $row->province->name ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $row->province_id . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('status_attribute', function ($row) {
                    $status     = $row->status == 'active' ? 'checked' : '';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->addColumn('districts', function ($row) {
                    $html = '<a class="text-info" href="' . route('directory.districts') . '?city=' . $row->id . '"> ' . number_format($row->districts->count()) . ' Kecamatan </a>';
                    return $html;
                })->rawColumns(['name', 'province',  'status_attribute', 'districts'])
                ->make(true);
        }

        return view('master.directory.city.index', ['page' => __('page.city.page'), 'breadcumb' => true], compact('params'));
    }

    public function createCity(Request $request)
    {
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('master.directory.city.create', ['page'  => __('page.city.add'), 'breadcumb' => true], compact('provinces'));
    }

    public function updateCity(Request $request, City $city)
    {
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('master.directory.city.update', ['page'  => __('page.city.update'), 'breadcumb' => true], compact('city', 'provinces'));
    }

    public function deleteCity(City $city)
    {
        $city->delete();
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    public function storeCity(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'province'  => 'required'
        ]);

        $this->directoryObserver->createCity($request);
        return redirect()->route('directory.cities')->with(['flash'  => __('general.success_add_data')]);
    }

    public function editCity(Request $request, City $city)
    {
        $this->validate($request, [
            'name'      => 'required',
            'province'  => 'required'
        ]);

        $this->directoryObserver->updateCity($request, $city);
        return redirect()->route('directory.cities')->with(['flash'  => __('general.success_update')]);
    }

    public function importCity(Request $request)
    {
        $this->validate($request, [
            'file'  => 'mimes:csv'
        ]);

        if ($request->file) {

            $import = Excel::toArray(new CityImport(), $request->file('file'));


            if (count($import[0]) > 0) {

                try {

                    DB::beginTransaction();

                    foreach ($import[0] as $d) {
                        if ($d['nm'] != null) {
                            $id         = $d['id'];
                            $id_str     = (string)$id;
                            $parts      = explode('.', $id_str);

                            $part1 = $parts[0];
                            $part2 = isset($parts[1]) ? str_pad($parts[1], 2, '0', STR_PAD_LEFT) : '00'; // 01

                            City::firstOrNew(
                                [
                                    'name'          => $d['nm'],
                                    'province_id'   => $part1,
                                    'id'            => $d['id'],
                                    'type'          => $d['type']
                                ]
                            )->save();
                        }
                    }

                    DB::commit();

                    return redirect()->back()->with(['flash'    => __('general.success_import')]);
                } catch (\Exception $e) {

                    DB::rollBack();

                    return redirect()->back()->with(['gagal'    => $e->getMessage()]);
                }
            } else {
                return redirect()->back()->with(['gagal'    => __('general.file_not_reader')]);
            }
        }
    }


    public function changeCityStatus(City $city)
    {
        $city->update([
            'status'        => $city->status == 'active' ? 'no' : 'active'
        ]);

        return response()->json([
            'message'  => __('general.success_update'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Districts
    |--------------------------------------------------------------------------
    */

    public function districts(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $districts  = $this->directoryObserver->getDistrict($request);

            return DataTables::of($districts)
                ->addColumn('province', function ($row) {
                    $province   = $row->city->province->name ?? '';
                    $provinceID = $row->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->city->type ?? '';
                    $cityName = $row->city->name ?? '';
                    $html = '<a href="' . route('directory.districts') . '?city=' . $row->city_id . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('stores', function ($row) {
                    $html = '<a class="text-info" href="' . route('stores') . '?district=' . $row->id . '"> ' . number_format($row->store->count()) . ' Pelanggan </a>';
                    return $html;
                })->rawColumns(['province',  'city', 'stores'])
                ->make(true);
        }

        return view('master.directory.district.index', ['page' => __('page.district.page'), 'breadcumb' => true], compact('params'));
    }

    public function createDistrict(Request $request)
    {
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('master.directory.district.create', ['page'  => __('page.district.add'), 'breadcumb' => true], compact('provinces'));
    }

    public function updateDistrict(Request $request, District $district)
    {
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        return view('master.directory.district.update', ['page'  => __('page.district.update'), 'breadcumb' => true], compact('district', 'provinces'));
    }

    public function storeDistrict(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'city'      => 'required'
        ]);

        $this->directoryObserver->createDistrict($request);
        return redirect()->route('directory.districts')->with(['flash'  => __('general.success_add_data')]);
    }

    public function editDistrict(Request $request, District $district)
    {
        $this->validate($request, [
            'name'      => 'required',
            'city'      => 'required'
        ]);

        $this->directoryObserver->updateDistrict($request, $district);
        return redirect()->route('directory.districts')->with(['flash'  => __('general.success_update')]);
    }

    public function deleteDistrict(District $district)
    {
        $district->delete();
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    public function importDistrict(Request $request)
    {
        $this->validate($request, [
            'file'  => 'mimes:csv'
        ]);

        if ($request->file) {

            $import = Excel::toArray(new DistrictImport(), $request->file('file'));

            if (count($import[0]) > 0) {



                try {

                    DB::beginTransaction();

                    foreach ($import[0] as $d) {
                        if ($d['nm'] != null) {
                            District::firstOrNew(
                                [
                                    'id'            => $d['id'],
                                    'name'          => $d['nm'],
                                    'city_id'       => substr((string)$d['id'], 0, 5)
                                ]
                            )->save();
                        }
                    }
                    DB::commit();

                    return redirect()->back()->with(['flash'    => __('general.success_import')]);
                } catch (\Exception $e) {

                    DB::rollBack();

                    return redirect()->back()->with(['gagal'    => $e->getMessage()]);
                }
            } else {
                return redirect()->back()->with(['gagal'    => __('general.file_not_reader')]);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Sub Districts
    |--------------------------------------------------------------------------
    */

    public function subDistricts(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $districts  = $this->directoryObserver->getSubDistrict($request);

            return DataTables::of($districts)
                ->addColumn('province', function ($row) {
                    $province   = $row->district->city->province->name ?? '';
                    $provinceID = $row->district->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->district->city->type ?? '';
                    $cityName = $row->district->city->name ?? '';
                    $html = '<a href="' . route('directory.districts') . '?city=' . $row->city_id . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('district', function ($row) { 
                    $disctrictName  = $row->district->name ?? '';
                    $html = '<a href="' . route('directory.subdistricts') . '?district=' . $row->district_id . '" class="text-info">' . $disctrictName . '</a>';
                    return $html;
                })->rawColumns(['province',  'city', 'district'])
                ->make(true);
        }

        return view('master.directory.subdistrict.index', ['page' => __('page.subdistrict.page'), 'breadcumb' => true], compact('params'));
    }
}
