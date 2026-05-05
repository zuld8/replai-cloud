<?php

namespace App\Observers\Saas;

use Illuminate\Support\Facades\Http;

class DuitkuObserver
{
    public function getPaymentMethods($data, $mode = 'no')
    {

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($this->getUrl($mode) . "/merchant/paymentmethod/getpaymentmethod", [
            'merchantcode'      => $data['merchant_code'],
            'amount'            => $data['amount'],
            'datetime'          => $data['date'],
            'signature'         => $data['signature']
        ]);

        return $response;
    }

    public function createTransaction($data, $apiKey, $mode)
    { 
        $response = Http::withHeaders([
            'Content-Type'      => 'application/json',
            'Content-Length'    => strlen(json_encode($data))
        ])->post($this->getUrl($mode) . "/merchant/v2/inquiry", $data);

        return $response;
    }

    private function getUrl($mode)
    {
        if ($mode == 'no') {
            return 'https://sandbox.duitku.com/webapi/api';
        }

        return 'https://passport.duitku.com/webapi/api';
    }
}
