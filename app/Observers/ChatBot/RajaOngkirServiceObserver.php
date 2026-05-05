<?php

namespace App\Observers\ChatBot;

use App\Models\Courier\CourierFineTunnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirServiceObserver
{
    public function trackingOngkir($courier, $noResi, $apiCode)
    {
        return Http::withHeaders([
            'key'           => $apiCode
        ])->post('https://rajaongkir.komerce.id/api/v1/track/waybill?awb=' . $noResi . '&courier=' . $courier);
    }

    public function checkOngkir($apiCode, $origin, $destination, $weight, $expedision)
    {
        return  Http::withHeaders([
            'key' => $apiCode,
            'Accept' => 'application/json',
            // jangan set content-type manual, Laravel akan urus otomatis
        ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'origin'      => (string)$origin,
            'destination' => (string)$destination,
            'weight'      => (int)$weight,
            'courier'     => $expedision,
        ]);
    }

    public function getDistrict($city, $district, $apiCode)
    {
        return Http::withHeaders([
            'key'           => $apiCode
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . urlencode($city . ', ' . $district));
    }

    public function getDistrictBySearch(Request $request, $apiCode)
    {
        return Http::withHeaders([
            'key'           => $apiCode
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?limit=10&search=' . urlencode($request->term));
    }
}
