<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Log;
use App\Models\Setting; 
use PHPMailer\PHPMailer\PHPMailer;

class SendPromotionEmailJob
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
            return $q->where("use", 'email');
        })->first();

        if ($getData) {

            $log = Log::create([
                'description'   => __('blash.blash_description', [
                    'name'          => $getData->store->name ?? '',
                    'schedule'      => $getData->parent->name ?? ''
                ]),
                'type'          => 'email',
                'merchant_id'   => $getData->store->merchant_id ?? null,
                'business_id'   => $getData->store->business_id ?? null
            ]);

            $setting    = Setting::where("id", ($getData->store->business_id ?? null))->first([
                'email_sender',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_from_address',
                'mail_encryption',
                'mail_from_name',
            ]); 


            if ($setting && $getData->store) {
                if (($getData->store->merchant_id ?? null) != null) {

                    $merchant   = $getData->store->merchant ?? null;
                    if ($setting != null && $merchant != null) {
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

                        if ($transaction->limit_email_option == 'yes') {
                            if ($setting->email_sender >= $transaction->email_limit) {
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

                $setting->update([
                    'email_sender'       => $setting->email_sender + 1
                ]);

                $email          = $getData->email ?? '';
                $message        = $getData->parent->template->message ?? null;
                $template       = $getData->parent->template ?? null;

                if ($message != null) {


                    if ($email != '') {

                        $mail = new PHPMailer(true);

                        try {

                            $mail->SMTPDebug = 0;
                            $mail->isSMTP();
                            $mail->Host         = $setting->mail_host;
                            $mail->SMTPAuth     = true;
                            $mail->Username     = $setting->mail_username;
                            $mail->Password     = $setting->mail_password;
                            $mail->SMTPSecure   = $setting->mail_encryption;
                            $mail->Port         = $setting->mail_port;

                            $mail->setFrom($setting->mail_from_address, $setting->mail_from_name);
                            $mail->addAddress($getData->store->email);

                            $mail->isHTML(true);

                            $mail->Subject = $template->name;
                            $mail->Body    = view('mail.promotions', ['details'  => $getData]);

                            if (!$mail->send()) {
                                $getData->update([
                                    'status'        => 'yes',
                                    'reports'       => $mail->ErrorInfo
                                ]);

                                $log->update([
                                    'error'     => $mail->ErrorInfo,
                                    'status'    => 'error'
                                ]);
                            } else {
                                $getData->update([
                                    'status'    => 'yes',
                                ]);

                                $log->update([
                                    'status'    => 'success'
                                ]);
                            }
                        } catch (\Exception $e) {
                            $getData->update([
                                'status'        => 'yes',
                                'reports'       => $e->getMessage()
                            ]);

                            $log->update([
                                'error'     => $e->getMessage(),
                                'status'    => 'error'
                            ]);
                        }
                    } else {
                        $getData->update([
                            'status'        => 'yes',
                            'reports'       => __('blash.email_not_found')
                        ]);

                        $log->update([
                            'error'     =>  __('blash.email_not_found'),
                            'status'    => 'error'
                        ]);
                    }
                }
            }

            $getData->update([
                'status'    => 'yes',
            ]);

            $log->update([
                'status'    => 'success'
            ]);
        }
    }
}
