<?php

namespace App\Observers\ChatBot;

use Illuminate\Support\Facades\Http;

class BiteshipServiceObserver
{

    public function checkOngkir($apiCode, $origin, $destination, $weight, $expedision, $quantity)
    {
        return  Http::withToken($apiCode)->post('https://api.biteship.com/v1/rates/couriers', [
            'origin_postal_code'      => (string)$origin,
            'destination_postal_code' => (string)$destination,
            'couriers'                => $expedision,
            'items'                   => [
                [
                    'weight'                => (int)$weight,
                    'quantity'              => (int)$quantity
                ]
            ]
        ]);
    }
}
