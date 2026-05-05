<?php

namespace App\Observers\Store;

use App\Models\Setting;
use App\Models\Store\Scrapping;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class StoreScrappingObserver
{
    public function getData(Request $request)
    {
        return Scrapping::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->category ? $q->where("category_id", $request->category) : '';
        })->where(function ($q) use ($request) {
            return $request->district ? $q->where("district_id", $request->district) : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where('scrapping_method', 'gmaps')->orderBy('created_at', 'desc');
    }

    public function createData(Request $request)
    {
        return Scrapping::create([
            'category_id'           => $request->category,
            'district_id'           => $request->district,
            'scrapping_method'      => 'gmaps',
            'name'                  => $request->name,
            'schedule'              => $request->schedule,
        ]);
    }

    public function updateData(Request $request, Scrapping $scrapping)
    {
        $scrapping->update([
            'category_id'           => $request->category,
            'district_id'           => $request->district,
            'name'                  => $request->name,
            'scrapping_method'      => 'gmaps',
            'schedule'              => $request->schedule,
        ]);
    }

    public function deleteData(Scrapping $scrapping)
    {
        $scrapping->delete();
    }

    public function scrappingData(Scrapping $scrapping)
    {
        $settings   = Setting::where("id", $scrapping->business_id)->first(['gmap_key', 'scrapp_phone', 'scrapp_phone_whatsapp', 'phone_country_code', 'scrapp_counter']);
        $keyword    = $scrapping->name ?? '';
        $cityType   = $scrapping->district->city->type ?? '';
        $city       = $scrapping->district->city->name ?? '';
        $district   = $scrapping->district->name ?? '';
        $location   = $cityType . ' ' . $city . ' ' . $district;
        $apiKey     = $settings->gmap_key;

        $searchUrl  = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        $searchParams = [
            'query' => $keyword . ' in ' . $location,
            'key'   => $apiKey
        ];

        $client     = new Client();
        $response   = $client->request('GET', $searchUrl, ['query' => $searchParams]);
        $data       = json_decode($response->getBody(), true);

        if ($data['status'] == 'OK') {
            if (isset($data['results'])) {
                foreach ($data['results'] as $result) {
                    $placeId = $result['place_id'];

                    $detailsParams = [
                        'place_id'  => $placeId,
                        'key'       => $apiKey
                    ];

                    $detailsResponse = $this->scrapDetails($detailsParams);
                    $detailsData     = json_decode($detailsResponse->getBody(), true);

                    if (isset($detailsData['result'])) {
                        $name       = $detailsData['result']['name'];
                        $address    = $detailsData['result']['formatted_address'];
                        $phone      = isset($detailsData['result']['formatted_phone_number']) ? $detailsData['result']['formatted_phone_number'] : null;

                        if ($phone != null) {
                            $phone = preg_replace('/\D/', '', $phone);

                            if (substr($phone, 0, 1) == 0) {
                                $phone  = $settings->phone_country_code . substr($phone, 1, 15);
                            }
                        }

                        if ($phone != null) {

                            if ($settings->scrapp_phone_whatsapp == 'must_whatsapp' && substr($phone, 0, 4) != '6221' || $settings->scrapp_phone_whatsapp == 'all') {
                                $allowInsert    = true;

                                if ($settings->scrapp_phone == 'protect_double') {
                                    $sameStore =  Store::where("phone", $phone)->count();
                                    if ($sameStore > 0) {
                                        $allowInsert = false;
                                    }
                                }

                                if ($allowInsert) {
                                    Store::create([
                                        'category_id'       => $scrapping->category_id,
                                        'district_id'       => $scrapping->district_id,
                                        'name'              => $name,
                                        'phone'             => $phone,
                                        'address'           => $address,
                                        'scrapping_id'      => $scrapping->id,
                                        'business_id'       => $scrapping->business_id,
                                        'merchant_id'       => $scrapping->merchant_id
                                    ]);
                                }
                            }

                            $settings->update([
                                'scrapp_counter'       => $settings->scrapp_counter + 1
                            ]);
                        }
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function scrapDetails(array $data)
    {
        $client = new Client();
        $url    = 'https://maps.googleapis.com/maps/api/place/details/json';
        return $client->request('GET', $url, ['query' => $data]);
    }
}
