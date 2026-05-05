<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Observers\Master\TemplateObserver;
use App\Observers\Saas\NotificationObserver;
use App\Observers\WhatsappDeviceObserver;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Notification Setting Controller
    |--------------------------------------------------------------------------
    */

    protected $notificationSettingObserver;
    protected $messageTemplateObserver;
    protected $deviceObserver;

    public function __construct(NotificationObserver $notificationObserver, TemplateObserver $messageTemplateObserver, WhatsappDeviceObserver $deviceObserver)
    {
        $this->notificationSettingObserver      = $notificationObserver;
        $this->messageTemplateObserver          = $messageTemplateObserver;
        $this->deviceObserver                   = $deviceObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Page For Notification Setting
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $setting        = $this->notificationSettingObserver->getData();
        $watemplates    = $this->messageTemplateObserver->getData($request)->where('merchant_id', null)->where('type', 'whatsapp')->get(['id', 'name']);
        $mailtemplates  = $this->messageTemplateObserver->getData($request)->where('merchant_id', null)->where('type', 'email')->get(['id', 'name']);
        $devices        = $this->deviceObserver->getData($request)->where('merchant_id', null)->get(['id', 'name', 'phone']);
        $wabaDevices    = \App\Models\WhatsappKeyAccount::where('merchant_id', null)->where('status', 'active')->get(['id', 'phone', 'meta_data']);
        return view('admin.settings.notification', ['page'  => __('page.setting.notification'), 'breadcumb' => false], compact('setting', 'watemplates', 'mailtemplates', 'devices', 'wabaDevices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Update Notification Setting
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {

        $this->validate($request, [
            'waba_device'                           => 'nullable|uuid',
            'whatsapp_register'                     => 'required|in:yes,no',
            'whatsapp_register_template'            => 'required_if:whatsapp_register,yes|uuid',
            'whatsapp_buy_package'                  => 'required|in:yes,no',
            'whatsapp_buy_package_template'         => 'required_if:whatsapp_buy_package,yes|uuid',
            'whatsapp_package_payment'              => 'required|in:yes,no',
            'whatsapp_package_payment_template'     => 'required_if:whatsapp_package_payment,yes|uuid',
            'whatsapp_package_user'                 => 'required|in:yes,no',
            'whatsapp_package_user_template'        => 'required_if:whatsapp_package_user,yes|uuid',
            'whatsapp_approval_payment'             => 'required|in:yes,no',
            'whatsapp_approval_payment_template'    => 'required_if:whatsapp_approval_payment,yes|uuid',
            'email_register'                        => 'required|in:yes,no',
            'email_register_template'               => 'required_if:email_register,yes|uuid',
            'email_buy_package'                     => 'required|in:yes,no',
            'email_buy_package_template'            => 'required_if:email_buy_package,yes|uuid',
            'email_package_payment'                 => 'required|in:yes,no',
            'email_package_payment_template'        => 'required_if:email_package_payment,yes|uuid',
            'email_package_user'                    => 'required|in:yes,no',
            'email_package_user_template'           => 'required_if:email_package_user,yes|uuid',
            'email_approval_payment'                => 'required|in:yes,no',
            'email_approval_payment_template'       => 'required_if:email_approval_payment,yes|uuid',
        ]);


        $this->notificationSettingObserver->update($request);

        return redirect()->back()->with(['flash'    => __('general.success_update')]);
    }
}
