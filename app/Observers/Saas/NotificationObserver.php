<?php

namespace App\Observers\Saas;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationObserver
{
    public function getData()
    {
        return NotificationSetting::with(['device' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'wabaDevice' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'buy_package_template_whatsapp'   => function ($query) {
            $query->withoutGlobalScopes();
        }, 'package_payment_template_whatsapp' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'package_user_template_whatsapp'  => function ($query) {
            $query->withoutGlobalScopes();
        }, 'buy_package_template_email'  => function ($query) {
            $query->withoutGlobalScopes();
        }, 'package_payment_template_email'  => function ($query) {
            $query->withoutGlobalScopes();
        }, 'package_user_template_email'  => function ($query) {
            $query->withoutGlobalScopes();
        }, 'approval_payment_template_email'  => function ($query) {
            $query->withoutGlobalScopes();
        }])->first();
    }

    public function update(Request $request)
    {
        $this->getData()->update([
            'received_notification'              => $request->received_notification,
            'received_email_notification'        => $request->received_email_notification,
            'device_notification'                => $request->device,
            'waba_device_notification'           => ($request->waba_device ?: null),
            'whatsapp_register'                  => $request->whatsapp_register,
            'whatsapp_register_template'         => $request->whatsapp_register_template,
            'whatsapp_buy_package'               => $request->whatsapp_buy_package,
            'whatsapp_buy_package_template'      => $request->whatsapp_buy_package_template,
            'whatsapp_package_payment'           => $request->whatsapp_package_payment,
            'whatsapp_package_payment_template'  => $request->whatsapp_package_payment_template,
            'whatsapp_package_user'              => $request->whatsapp_package_user,
            'whatsapp_package_user_template'     => $request->whatsapp_package_user_template,
            'whatsapp_approval_payment'          => $request->whatsapp_approval_payment,
            'whatsapp_approval_payment_template' => $request->whatsapp_approval_payment_template,
            'email_register'                     => $request->email_register,
            'email_register_template'            => $request->email_register_template,
            'email_buy_package'                  => $request->email_buy_package,
            'email_buy_package_template'         => $request->email_buy_package_template,
            'email_package_payment'              => $request->email_package_payment,
            'email_package_payment_template'     => $request->email_package_payment_template,
            'email_package_user'                 => $request->email_package_user,
            'email_package_user_template'        => $request->email_package_user_template,
            'email_approval_payment'             => $request->email_approval_payment,
            'email_approval_payment_template'    => $request->email_approval_payment_template,
        ]);
    }
}
