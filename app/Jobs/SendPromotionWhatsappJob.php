<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Log;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Observers\WhatsappNotificationObserver;
use App\Observers\WhatsappServiceObserver;

class SendPromotionWhatsappJob
{



    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */

    public function handle(): void
    {

        $getData    = BlashDetail::where("status", "no")->whereHas('parent', function ($q) {
            return $q->where("use", 'whatsapp');
        })->first();

        if ($getData) {

            $settings    = Setting::where("id", ($getData->store->business_id ?? null))->first(['whatsapp_sender', 'whatsapp_sender_notif', 'use_whatsapp', 'id']);
            $log        = Log::create([
                'description'   => __('blash.blash_description', [
                    'name'          => $getData->store->name ?? '',
                    'schedule'      => $getData->parent->name ?? ''
                ]),
                'merchant_id'   => $getData->store->merchant_id ?? null,
                'business_id'   => $getData->store->business_id ?? null,
                'type'          => 'whatsapp'
            ]);

            if (!$settings) {
                $log->update([
                    'status'        => 'error',
                    'error'         => 'Tidak dapat mendeteksi pengaturan'
                ]);
            }

            if ($getData->store->business_id != null) {

                $merchant   = $getData->store->merchant ?? null;
                if ($settings != null && $merchant != null) {
                    $transaction = $merchant->package_active;
                    if (!$transaction) {

                        $getData->update([
                            'status'        => 'yes',
                            'reports'       => 'Paket Langganan telah Berakhir'
                        ]);

                        $log->update([
                            'status'        => 'error',
                            'error'         => 'Paket Langganan telah Berakhir'
                        ]);
                    }

                    if ($transaction->limit_whatsapp_option == 'yes') {
                        if ($settings->whatsapp_sender >= $transaction->whatsapp_limit) {
                            $getData->update([
                                'status'        => 'yes',
                                'reports'       => 'Limit Pengiriman Harian Telah Habi'
                            ]);

                            $log->update([
                                'status'        => 'error',
                                'error'         => 'Limit Pengiriman harian telah habis'
                            ]);
                        }
                    }
                }
            }

            $settings->update([
                'whatsapp_sender'       => $settings->whatsapp_sender + 1
            ]);

            $whatsappNotificationObserver   = new WhatsappNotificationObserver();
            $whatsappServiceObserver        = new WhatsappServiceObserver();

            $storeName      = $getData->store->name ?? '';
            $categoryName   = $getData->store->category->name ?? '';
            $phone          = $getData->phone ?? '';
            $email          = $getData->email ?? '';
            $address        = $getData->store->address ?? '';
            $message        = $getData->parent->template->message ?? null;

            if ($message != null) {

                $message = str_replace(
                    ['{name}', '{category}', '{phone}', '{email}', '{address}'],
                    [$storeName, $categoryName, $phone, $email, $address],
                    $message
                );

                $whatsappAccounts   = $settings->use_whatsapp == 'external' ?  WhatsappKeyAccount::whereRaw('daily_send < limit_per_day')->where("status", "active") : WhatsappDevice::where(function ($q) {
                    return $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no");
                })->where(function ($q) use ($getData) {
                    return $getData->parent->devices != null ? $q->whereIn('id', explode(',', $getData->parent->devices)) : '';
                })->where("business_id", $settings->id)->where("status", "active");

                $messageVariable = array(
                    'message'           => urldecode($message),
                    'template_body'     => array(),
                    'whatsapp_key'      => '',
                    'whatsapp_session'  => '',
                    'file'              => $getData->parent->template->image != null ? asset($getData->parent->template->image) : '',
                    'phone'             => $getData->store->phone ?? null,
                    'type'              => $getData->parent->template->type_content ?? 'description',
                    'datas'             => json_decode($getData->parent->template->button_or_list, true)
                );

                $status = false;
                if ($getData->parent->whatsapp_sender_notif === 'sequence') {
                    if ($whatsappAccounts->count() > 0) {
                        $account                                = $whatsappAccounts->first();
                        $messageVariable['whatsapp_key']        = $settings->use_whatsapp == 'external' ? $account->whatsapp_key : $account->id;
                        $messageVariable['whatsapp_session']    = $settings->use_whatsapp == 'external' ? $account->whatsapp_session : $account->id;
                        $account->daily_send                    += 1;
                        $account->save();
                        $status = true;
                    }
                } elseif ($getData->parent->whatsapp_sender_notif === 'spin') {
                    if ($whatsappAccounts->count() > 0) {
                        $accountData                            = collect($whatsappAccounts->get())->shift();
                        $account                                = $whatsappAccounts->where("id", $accountData->id)->first();
                        $messageVariable['whatsapp_key']        = $settings->use_whatsapp == 'external' ? $account->whatsapp_key : $account->id;
                        $messageVariable['whatsapp_session']    = $settings->use_whatsapp == 'external' ? $account->whatsapp_session : $account->id;
                        $account->daily_send += 1;
                        $account->save();
                        $status = true;
                    }
                } elseif ($getData->parent->whatsapp_sender_notif === 'random') {
                    if ($whatsappAccounts->count() > 0) {
                        $accountData                            = collect($whatsappAccounts->get())->random();
                        $account                                = $whatsappAccounts->where("id", $accountData->id)->first();
                        $messageVariable['whatsapp_key']        = $settings->use_whatsapp == 'external' ? $account->whatsapp_key : $account->id;
                        $messageVariable['whatsapp_session']    = $settings->use_whatsapp == 'external' ? $account->whatsapp_session : $account->id;
                        $account->daily_send += 1;
                        $account->save();
                        $status = true;
                    }
                }

                if ($status == true) {
                    if ($phone != '') {

                        if ($settings->use_whatsapp == 'external') {
                            $result = $whatsappNotificationObserver->whatsAppNotif($messageVariable);

                            if ($result->status() == 200) {
                                $getData->update([
                                    'status'    => 'yes',
                                    'reports'   => null
                                ]);

                                $log->update([
                                    'status'    => 'success'
                                ]);
                            } else {

                                $resultBody     = json_decode($result->getBody());
                                $getData->update([
                                    'status'        => 'yes',
                                    'reports'       => isset($resultBody->error) ? $resultBody->error : __('blash.unknown_error')
                                ]);

                                $log->update([
                                    'status'        => 'error',
                                    'error'         => isset($resultBody->error) ? $resultBody->error : __('blash.unknown_error')
                                ]);
                            }
                        } else {
                            $result = $whatsappServiceObserver->sendMessage($phone, $messageVariable['whatsapp_key'], $messageVariable['message'], $messageVariable['file'], $messageVariable['type'], $messageVariable['datas']);

                            if ($result['status'] == 200) {
                                $getData->update([
                                    'status'    => 'yes',
                                    'reports'   => null
                                ]);

                                $log->update([
                                    'status'    => 'success'
                                ]);
                            } else {
                                $getData->update([
                                    'status'        => 'yes',
                                    'reports'       => $result['message']
                                ]);

                                $log->update([
                                    'status'        => 'error',
                                    'reports'       => $result['message']
                                ]);
                            }
                        }
                    } else {
                        $getData->update([
                            'status'        => 'yes',
                            'reports'       => __('blash.phone_nothing')
                        ]);

                        $log->update([
                            'status'        => 'error',
                            'error'         => __('blash.wa_not_registered')
                        ]);
                    }
                } else {
                    $log->update([
                        'status'        => 'error',
                        'error'         => __('blash.daily_limit')
                    ]);
                }
            }
        }
    }
}
