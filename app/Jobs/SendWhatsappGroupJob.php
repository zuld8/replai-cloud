<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Log;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsappGroupJob implements ShouldQueue
{
    use Queueable;

    protected $blast;

    /**
     * Create a new job instance.
     */

    public function __construct(BlashDetail $blast)
    {
        $this->blast        = $blast;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings   = Setting::where("id", ($this->blast->group->business_id ?? null))->first(['whatsapp_sender', 'whatsapp_sender_notif', 'id']);

        $log        = Log::create([
            'description'   => __('blash.blash_description', [
                'name'          => $this->blast->group->name ?? '',
                'schedule'      => $this->blast->parent->name ?? ''
            ]),
            'merchant_id'   => $this->blast->group->merchant_id ?? null,
            'business_id'   => $this->blast->group->business_id ?? null,
            'type'          => 'whatsapp_group'
        ]);

        if (!$settings) {
            $log->update([
                'status'        => 'error',
                'error'         => 'Tidak dapat mendeteksi pengaturan'
            ]);
        }

        if ($this->blast->group->business_id != null) {

            $merchant   = $this->blast->group->merchant ?? null;
            if ($settings != null && $merchant != null) {
                $transaction = $merchant->package_active;
                if (!$transaction) {

                    $this->blast->update([
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
                        $this->blast->update([
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

        $whatsappServiceObserver        = new WhatsappServiceObserver();

        $storeName      = $this->blast->group->name ?? '';
        $categoryName   = $this->blast->group->category->name ?? '';
        $phone          = $this->blast->phone ?? '';
        $email          = $this->blast->email ?? '';
        $address        = $this->blast->group->address ?? '';
        $message        = $this->blast->parent->template->message ?? null;

        if ($message != null) {

            $message = str_replace(
                ['{name}', '{category}', '{phone}', '{email}', '{address}'],
                [$storeName, $categoryName, $phone, $email, $address],
                $message
            );

            $whatsappAccounts   = WhatsappDevice::where('id', $this->blast->group->device_id)->where("status", "active")->first();

            if (!$whatsappAccounts) {
                $log->update([
                    'status'        => 'error',
                    'store_id'      => $this->blast->group_id,
                    'sending'       => now()->format('Y-m-d H:i:s'),
                    'text'          => 'Device tidak ditemukan atau tidak aktif',
                    'error'         => 'Device tidak ditemukan atau tidak aktif',
                ]); 

                return;
            }

            $messageVariable = array(
                'message'           => urldecode($message),
                'template_body'     => array(),
                'whatsapp_key'      => '',
                'whatsapp_session'  => '',
                'file'              => $this->blast->parent->template->image != null ? asset($this->blast->parent->template->image) : '',
                'phone'             => $this->blast->group->group_id ?? null,
                'type'              => $this->blast->parent->template->type_content ?? 'description',
                'datas'             => json_decode($this->blast->parent->template->button_or_list, true)
            ); 

            $account                                = $whatsappAccounts;
            $messageVariable['whatsapp_key']        = $account->id;
            $messageVariable['whatsapp_session']    = $account->id;
            $account->daily_send                    += 1;
            $account->save();
            $status = true;

            $business       = $this->blast->parent->business;
            if (($business->merchant ?? null) != null) {
                $topupLimit     = $business->package_active_topup->sisa_credit ?? 0;
                $packageCredit  = $business->package_active->sisa_credit ?? 0;
                $totalLimit     = ($topupLimit + $packageCredit);
                $usageCredit    = 1;

                if ($totalLimit < $usageCredit) {
                    $status  = false;
                    $messageVariable['message']     = 'Credit Token Anda tidak mencukupi';
                } else {
                    if ($packageCredit > 0) {
                        $business->package_active->update([
                            'using_credit_limit'        => $business->package_active->using_credit_limit + $usageCredit
                        ]);
                    } else {
                        $business->package_active_topup->update([
                            'using_credit_limit'        => $business->package_active_topup->using_credit_limit + $usageCredit
                        ]);
                    }
                }
            }

            if ($status == true) {
                if ($phone != '') {

                    $result = $whatsappServiceObserver->sendMessage($phone, $messageVariable['whatsapp_key'], $messageVariable['message'], $messageVariable['file'], $messageVariable['type'], $messageVariable['datas'], true);

                    if ($result['status'] == 200) {
                        $this->blast->update([
                            'status'    => 'yes',
                            'sending'   => now()->format('Y-m-d H:i:s'),
                            'phone'     => $this->blast->group->group_id ?? '',
                            'text'      => $messageVariable['message'],
                            'device_id' => $messageVariable['whatsapp_session'],
                            'reports'   => null
                        ]);

                        $log->update([
                            'status'    => 'success',
                            'store_id'  => $this->blast->group_id,
                            'sending'   => now()->format('Y-m-d H:i:s'),
                            'text'      => $messageVariable['message'],
                            'device_id' => $messageVariable['whatsapp_session'],
                        ]);
                    } else {
                        $this->blast->update([
                            'status'        => 'yes',
                            'sending'       => now()->format('Y-m-d H:i:s'),
                            'phone'     => $this->blast->group->group_id ?? '',
                            'text'          => $messageVariable['message'],
                            'device_id'     => $messageVariable['whatsapp_session'],
                            'sending_status' => 'no',
                            'reports'       => $result['message']
                        ]);

                        $log->update([
                            'status'        => 'error',
                            'store_id'      => $this->blast->group_id,
                            'sending'       => now()->format('Y-m-d H:i:s'),
                            'text'          => $messageVariable['message'],
                            'device_id'     => $messageVariable['whatsapp_session'],
                            'error'         => $result['message']
                        ]);
                    }
                } else {
                    $this->blast->update([
                        'status'        => 'yes',
                        'sending'       => now()->format('Y-m-d H:i:s'),
                        'phone'         => $this->blast->group->group_id ?? '',
                        'text'          => $messageVariable['message'],
                        'sending_status' => 'no',
                        'device_id'     => $messageVariable['whatsapp_session'],
                        'reports'       => __('blash.phone_nothing')
                    ]);

                    $log->update([
                        'status'        => 'error',
                        'store_id'      => $this->blast->group_id,
                        'sending'       => now()->format('Y-m-d H:i:s'),
                        'text'          => $messageVariable['message'],
                        'device_id'     => $messageVariable['whatsapp_session'],
                        'error'         => __('blash.wa_not_registered')
                    ]);
                }
            } else {
                $log->update([
                    'status'        => 'error',
                    'store_id'      => $this->blast->group_id,
                    'sending'       => now()->format('Y-m-d H:i:s'),
                    'text'          => $messageVariable['message'],
                    'error'         => __('blash.daily_limit')
                ]);
            }
        }
    }
}
