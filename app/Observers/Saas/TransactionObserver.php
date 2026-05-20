<?php

namespace App\Observers\Saas;

use App\Models\InternalSetting;
use App\Models\Package\Package;
use App\Models\Package\PackageTransaction;
use App\Models\Package\PackageTransactionPayment;
use App\Models\Setting;
use DateTime;
use Illuminate\Http\Request;

class TransactionObserver
{

    public function getData(Request $request, $type = 'package')
    {
        return PackageTransaction::where('type', $type)->where(function ($q) {
            return my_user()->role == 'user' ? $q->where("merchant_id", my_user()->merchant_id) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->bank ? $q->where("to_bank", $request->bank) : '';
        })->where(function ($q) use ($request) {
            return $request->merchant ? $q->where("merchant_id", $request->merchant) : '';
        })->orderBy("created_at", "desc");
    }


    public function createData(Setting $business, Package $package)
    {
        $addExpireDate          = now()->addDays((int)$package->add_days);
        $addAiResponse          = $package->ai_response;
        $aiResponse             = $package->ai_response;
        $getLastTransaction     = $this->getLastTransactionBusiness($business);
        $getDaysTransaction     = $this->getAddDaysTransaction($package, $getLastTransaction);
        $getLimitResponseAi     = $this->getAiResponse($package, $getLastTransaction);
        $settings               = InternalSetting::first(['tax']);
        $taxrate                = $settings->tax;
        $taxTotal               = $taxrate > 0 ? $taxrate / 100 * $package->price : 0;
        $invoiceNumber          = $this->generateInvoice();
        $refNo                  = 'SL' . date('Ymd') . '/' . $invoiceNumber;

        if ($getDaysTransaction['status'] == true) {
            $addExpireDate      = $getDaysTransaction['new_date'];
        }

        if ($getLimitResponseAi['status'] == true) {
            $addAiResponse      = $getLimitResponseAi['new_limit'];
        }

        return PackageTransaction::create([
            'invoice'                   => $invoiceNumber,
            'ref_no'                    => $refNo,
            'days_option'               => $package->days_option,
            'merchant_id'               => $business->merchant_id,
            'business_id'               => $business->id,
            'package_id'                => $package->id,
            'price'                     => $package->price,
            'expire_date'               => $package->days_option == 'limited' ? $addExpireDate  : null,
            'tax'                       => $taxrate,
            'storage'                   => $package->storage,
            'final_total'               => ($package->price + $taxTotal),
            'add_days'                  => $package->add_days,
            'limit_user_option'         => $package->limit_user_option,
            'users_limit'               => $package->users_limit,
            'limit_whatsapp_option'     => $package->limit_whatsapp_option,
            'limit_whatsapp_priode'     => $package->limit_whatsapp_priode,
            'whatsapp_limit'            => $package->whatsapp_limit,
            'limit_email_option'        => $package->limit_email_option,
            'limit_email_priode'        => $package->limit_email_priode,
            'email_limit'               => $package->email_limit,
            'limit_scrapp_option'       => $package->limit_scrapp_option,
            'limit_scrapp_priode'       => $package->limit_scrapp_priode,
            'scrapp_limit'              => $package->scrapp_limit,
            'limit_device'              => $package->limit_device,
            'device_limit'              => $package->device_limit,
            'limit_template'            => $package->limit_template,
            'template_limit'            => $package->template_limit,
            'limit_ai_training'         => $package->limit_ai_training,
            'ai_training_limit'         => $package->ai_training_limit,
            'limit_chatbot'             => $package->limit_chatbot,
            'chatbot_limit'             => $package->chatbot_limit,
            'status'                    => $package->trial_version == 'yes' ? 'success' : 'pending',
            'ai_response'               => $addAiResponse,
            'livechat_limit'            => $package->livechat_limit,
            'limit_livechat'            => $package->limit_livechat,
            'new_order_ai_response'     => $aiResponse,
            'limit_waba'                => $package->limit_waba ?? 'no',
            'waba_limit'                => $package->waba_limit ?? 0,
            'limit_instagram'           => $package->limit_instagram ?? 'no',
            'instagram'                 => $package->instagram ?? 0,
            'limit_messanger'           => $package->limit_messanger ?? 'no',
            'messanger'                 => $package->messanger ?? 0,
            'limit_telegram'            => $package->limit_telegram ?? 'no',
            'telegram'                  => $package->telegram ?? 0,
            'google_sheet'              => $package->google_sheet,
            'cek_ongkir'                => $package->cek_ongkir,
            'mua_limit'                 => $package->mua_limit,
            'mua_limit_optin'           => $package->mua_limit_optin,
            'max_per_upload'            => $package->max_per_upload,
            'max_total_rag'             => $package->max_total_rag,
        ]);
    }

    public function createTopUp(Setting $business, Request $request)
    {
        $settings               = InternalSetting::first(['tax', 'token_per_price', 'price_token']);
        $subtotal               = $settings->price_token * (int)$request->qty;
        $tokenCredit            = $settings->token_per_price * (int)$request->qty;
        $aiResponse             = $tokenCredit;
        $getLastTransaction     = $this->getLastTransactionBusiness($business, 'topup');
        $getLimitResponseAi     = $this->getAiResponseTopup($getLastTransaction, $tokenCredit);

        $invoiceNumber          = $this->generateInvoice();
        $refNo                  = 'SL' . date('Ymd') . '/' . $invoiceNumber;



        if ($getLimitResponseAi['status'] == true) {
            $tokenCredit      = $getLimitResponseAi['new_limit'];
        }

        return PackageTransaction::create([
            'invoice'                   => $invoiceNumber,
            'ref_no'                    => $refNo,
            'merchant_id'               => $business->merchant_id,
            'business_id'               => $business->id,
            'add_days'                  => 0,
            'days_option'               => 'unlimited',
            'final_total'               => $subtotal,
            'ai_response'               => $tokenCredit,
            'new_order_ai_response'     => $aiResponse,
            'type'                      => 'topup'
        ]);
    }

    public function createMua(Setting $business, Request $request)
    {
        $settings               = InternalSetting::first(['tax', 'mua_per_price', 'price_mua']);
        $subtotal               = $settings->price_mua * (int)$request->qty;
        $tokenCredit            = $settings->mua_per_price * (int)$request->qty;
        $aiResponse             = $tokenCredit;
        $getLastTransaction     = $this->getLastTransactionBusiness($business, 'mua');
        $getLimitResponseAi     = $this->getAiResponseMua($getLastTransaction, $tokenCredit);

        $invoiceNumber          = $this->generateInvoice();
        $refNo                  = 'SL' . date('Ymd') . '/' . $invoiceNumber;

        if ($getLimitResponseAi['status'] == true) {
            $tokenCredit      = $getLimitResponseAi['new_limit'];
        }

        return PackageTransaction::create([
            'invoice'                   => $invoiceNumber,
            'ref_no'                    => $refNo,
            'merchant_id'               => $business->merchant_id,
            'business_id'               => $business->id,
            'add_days'                  => 0,
            'days_option'               => 'unlimited',
            'final_total'               => $subtotal,
            'mua_limit'                 => $tokenCredit,
            'new_order_mua_limit'       => $aiResponse,
            'type'                      => 'mua'
        ]);
    }

    public function createStorage(Setting $business, Package $package)
    {
        $addExpireDate          = now()->addDays((int)$package->add_days);
        $getLastTransaction     = $this->getLastTransactionBusiness($business, 'storage');
        $getDaysTransaction     = $this->getAddDaysTransaction($package, $getLastTransaction);
        $settings               = InternalSetting::first(['tax']);
        $taxrate                = $settings->tax;
        $taxTotal               = $taxrate > 0 ? $taxrate / 100 * $package->price : 0;
        $invoiceNumber          = $this->generateInvoice();
        $refNo                  = 'SL' . date('Ymd') . '/' . $invoiceNumber;

        if ($getDaysTransaction['status'] == true) {
            $addExpireDate      = $getDaysTransaction['new_date'];
        }

        return PackageTransaction::create([
            'invoice'                   => $invoiceNumber,
            'ref_no'                    => $refNo,
            'days_option'               => 'limited',
            'type'                      => 'storage',
            'merchant_id'               => $business->merchant_id,
            'storage'                   => $package->storage,
            'business_id'               => $business->id,
            'package_id'                => $package->id,
            'price'                     => $package->price,
            'expire_date'               => $package->days_option == 'limited' ? $addExpireDate  : null,
            'tax'                       => $taxrate,
            'final_total'               => ($package->price + $taxTotal),
            'add_days'                  => $package->add_days,
        ]);
    }


    public function createPayment(Request $request, PackageTransaction $transaction, String $image)
    {
        return PackageTransactionPayment::create([
            'package_transaction_id'    => $transaction->id,
            'amount'                    => $request->amount ?? $transaction->final_total,
            'method'                    => 'bank',
            'to_bank'                   => $request->to_bank,
            'bank_name'                 => $request->bank_name,
            'bank_number'               => $request->bank_number,
            'file'                      => $image,
        ]);
    }

    public function getLastTransactionBusiness(Setting $business, String $type = 'package')
    {
        return PackageTransaction::where("status", "success")->where('type', $type)->where("business_id", $business->id)->orderBy("created_at", "desc")->first(['id', 'expire_date', 'ai_response', 'using_credit_limit']);
    }

    public function getAddDaysTransaction($package, $getLastTransaction)
    {

        if ($getLastTransaction != null) {
            if ($getLastTransaction->expire_date >= now()) {
                $datetime1 = new DateTime($getLastTransaction->expire_date);
                $datetime2 = new DateTime(now());
                $interval = $datetime1->diff($datetime2);

                $totalY     = 0;
                if ($interval->y > 0) {
                    $totalY     = $interval->y * 365;
                }

                $totalM     = 0;
                if ($interval->m > 0) {
                    $totalM     = $interval->m * 30;
                }

                $addDays = $package->add_days + ($interval->d + $totalY + $totalM);

                return array(
                    'status'    => true,
                    'new_date'  => now()->addDays($addDays)
                );
            }
        }

        return array(
            'status'    => false
        );
    }

    public function getAiResponse($package, $getLastTransaction)
    {

        if ($getLastTransaction != null) {

            if ($getLastTransaction->expire_date >= now()) {

                if ($getLastTransaction->ai_response > $getLastTransaction->using_credit_limit) {
                    $sisaLimit  = $getLastTransaction->ai_response - $getLastTransaction->using_credit_limit;
                    $newLimit   = $package->ai_response + $sisaLimit;

                    return array(
                        'status'    => true,
                        'new_limit' => (int)$newLimit
                    );
                }
            }
        }

        return array(
            'status'    => false
        );
    }

    public function getAiResponseTopup($getLastTransaction, $addLimit)
    {
        if ($getLastTransaction != null) {
            if ($getLastTransaction->ai_response > $getLastTransaction->using_credit_limit) {
                $sisaLimit  = $getLastTransaction->ai_response - $getLastTransaction->using_credit_limit;
                $newLimit   = $addLimit + $sisaLimit;

                return array(
                    'status'    => true,
                    'new_limit' => (int)$newLimit
                );
            }
        }

        return array(
            'status'    => false
        );
    }

    public function getAiResponseMua($getLastTransaction, $addLimit)
    {
        if ($getLastTransaction != null) {
            $newLimit   = $getLastTransaction->mua_limit + $addLimit;

            return array(
                'status'    => true,
                'new_limit' => (int)$newLimit
            );
        }

        return array(
            'status'    => false
        );
    }


    public function generateInvoice()
    {
        $getTransaction   = PackageTransaction::whereDate("created_at", date("Y-m-d"))->count() + 1;
        $invoiceNumber    = sprintf("%05d", $getTransaction);
        return $invoiceNumber;
    }
}
