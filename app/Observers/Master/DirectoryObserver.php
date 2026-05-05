<?php

namespace App\Observers\Master;

use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\Province;
use App\Models\Master\SubDisctrict;
use Illuminate\Http\Request;

class DirectoryObserver
{
    public function getProvince(Request $request)
    {
        return Province::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function getCity(Request $request)
    {
        return City::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->province ? $q->where("province_id", $request->province) : '';
        })->where(function ($q) use ($request) {
            return isset($request->search['value']) ? $q->where('name', 'like', '%' . $request->search['value'] . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function getDistrict(Request $request)
    {
        return District::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->city ? $q->where("city_id", $request->city) : '';
        })->where(function ($q) use ($request) {
            return isset($request->search['value']) ? $q->where('name', 'like', '%' . $request->search['value'] . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function getSubDistrict(Request $request)
    {
        return SubDisctrict::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->district ? $q->where("district_id", $request->district) : '';
        })->where(function ($q) use ($request) {
            return isset($request->search['value']) ? $q->where('name', 'like', '%' . $request->search['value'] . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function createProvince(Request $request)
    {
        return Province::create([
            'name'      => $request->name,
        ]);
    }

    public function updateProvince(Request $request, Province $province)
    {
        $province->update([
            'name'      => $request->name
        ]);
    }

    public function createCity(Request $request)
    {
        return City::create([
            'name'          => $request->name,
            'type'          => $request->type,
            'province_id'   => $request->province
        ]);
    }

    public function updateCity(Request $request, City $city)
    {
        $city->update([
            'name'          => $request->name,
            'type'          => $request->type,
            'province_id'   => $request->province
        ]);
    }

    public function createDistrict(Request $request)
    {
        return District::create([
            'name'          => $request->name,
            'city_id'       => $request->city
        ]);
    }

    public function updateDistrict(Request $request, District $district)
    {
        $district->update([
            'name'          => $request->name,
            'city_id'       => $request->city
        ]);
    }
}
