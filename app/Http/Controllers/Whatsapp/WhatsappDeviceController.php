<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Observers\ChatBot\FineTunnelObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\UserObserver;
use App\Observers\WhatsappDeviceObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;

class WhatsappDeviceController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Whatsapp Device Controllers
    |--------------------------------------------------------------------------
    */

    protected $whatsappDeviceObserver;
    protected $whatsappServiceObserver;
    protected $fineTunnelObserver;
    protected $templateObserver;
    protected $usersObserver;

    public function __construct(WhatsappDeviceObserver $whatsappDeviceObserver, WhatsappServiceObserver $whatsappServiceObserver, FineTunnelObserver $fineTunnelObserver, TemplateObserver $templateObserver, UserObserver $userObserver)
    {
        $this->whatsappDeviceObserver   = $whatsappDeviceObserver;
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
        $this->fineTunnelObserver       = $fineTunnelObserver;
        $this->templateObserver         = $templateObserver;
        $this->usersObserver            = $userObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Whatsapp Device List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $summary    = [
            'all'           => $this->whatsappDeviceObserver->getData($request)->count(),
            'active'        => $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->count(),
            'not_active'    => $this->whatsappDeviceObserver->getData($request)->where('status', 'no_active')->count(),
        ];

        $device     = $this->whatsappDeviceObserver->getData($request)->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->get();
        return view('device.index', ['page'    => __('page.device_account'), 'breadcumb' => true], compact('device', 'summary'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        return view('device.create', ['page'   => __('page.add_device'), 'breadcumb' => true], compact('fineTunnels', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, WhatsappDevice $device)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        return view('device.update', ['page'   => __('page.device.edit'), 'breadcumb' => true], compact('fineTunnels', 'device', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Scan Qr Page
    |--------------------------------------------------------------------------
    */

    public function scan(WhatsappDevice $device)
    {
        return view('device.scan', ['page'  => __('page.scan_qr_device'), 'breadcumb' => true], compact('device'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Delete Whatsapp Device
    |--------------------------------------------------------------------------
    */

    public function delete(WhatsappDevice $device)
    {
        try {
            if ($device->status == 'active') {
                $this->whatsappServiceObserver->deleteSession($device);
            }
        } catch (\Exception $e) {
        }

        $this->whatsappDeviceObserver->deleteData($device);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Store Data to Database
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'                  => 'required',
            'phone'                 => 'required|numeric',
            'limit'                 => 'required_if:daily_limit,yes',
            'days'                  => 'required_if:certain_day,yes',
            'start_time'            => 'required_if:certain_time,yes',
            'end_time'              => 'required_if:certain_time,yes',
            'method'                => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:method,ai,all',
            'agent'                 => 'required|array'
        ]);

        $validationCheck = $this->whatsappDeviceObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.device_limit')]);
        }

        $device = $this->whatsappDeviceObserver->createData($request);
        return redirect()->route('device.scan', $device->id)->with(['flash' => __('general.success_add_data')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Data to Database
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, WhatsappDevice $device)
    {
        $this->validate($request, [
            'name'      => 'required',
            'limit'                 => 'required_if:daily_limit,yes',
            'days'                  => 'required_if:certain_day,yes',
            'start_time'            => 'required_if:certain_time,yes',
            'end_time'              => 'required_if:certain_time,yes',
            'method'                => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:method,ai,all',
            'agent'                 => 'required|array'
        ]);

        $this->whatsappDeviceObserver->updateData($request, $device);
        return redirect()->route('device')->with(['flash' => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Check Whatsapp Server Session
    |--------------------------------------------------------------------------
    */

    public function checkSession(Request $request, WhatsappDevice $device)
    {

        $response       = $this->whatsappServiceObserver->checkSession($device);
        $device->status = $response->status() == 200 ? 'active' : 'no_active';

        if ($response->status() == 200) {
            $res = json_decode($response->body());
            if (isset($res->data->userinfo)) {
                $phone = str_replace('@s.whatsapp.net', '', $res->data->userinfo->id);
                $phone = explode(':', $phone);
                $phone = $phone[0] ?? null;
                $device->phone = $phone;
            }
        }
        $device->save();

        $message = $response->status() == 200 ? 'Koneksi ' : null;

        return response()->json(['message' => $message, 'connected' => $response->status() == 200 ? true : false]);
    }


    /*
    |--------------------------------------------------------------------------
    | 9. Create Whatsapp Session
    |--------------------------------------------------------------------------
    */

    public function createSession(Request $request, WhatsappDevice $device)
    {

        $response = $this->whatsappServiceObserver->createSession($device);
 
        if ($response->status() == 200) {
            $body               = json_decode($response->body());
            $data['qr']         = $body->data->qr;
            $data['message']    = $body->message;

            $device->update([
                'whatsapp_key'  => $body->data->qr ?? ''
            ]);

            return response()->json($data);
        } elseif ($response->status() == 409) {
            $data['qr']      = $device->whatsapp_key;
            $data['message'] = 'success';
            return response()->json($data);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 10. Delete Whatsapp Session
    |--------------------------------------------------------------------------
    */

    public function logoutSession(Request $request, WhatsappDevice $device)
    {
        $device->update([
            'status'    => 'no_active'
        ]);

        $this->whatsappServiceObserver->deleteSession($device);
        return response()->json(['message' => __('master.device.success_logout')]);
    }



    /*
    |--------------------------------------------------------------------------
    | 11. Set Any Auto Reply Chats
    |--------------------------------------------------------------------------
    */

    public function updateAutoReply(Request $request, WhatsappDevice $device)
    {

        $this->validate($request, [
            'reply_chat'        => 'required|in:yes,no',
            'reply_method'      => 'required|in:template,text',
            'reply_template'    => 'required_if:reply_method,template',
            'reply_text'        => 'required_if:reply_method,text',
        ]);

        $this->whatsappDeviceObserver->setAutoReply($device, $request);
        return redirect()->route('device')->with(['flash'   => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 12. Page For Update AutoReply
    |--------------------------------------------------------------------------
    */

    public function autoReply(WhatsappDevice $device, Request $request)
    {
        $templates      = $this->templateObserver->getData($request)->where('for_waba', 'no')->where('type', 'whatsapp')->get(['id', 'name']);
        return view('device.setting', ['page'    => __('page.device.greeting'), 'breadcumb' => true], compact('device', 'templates'));
    }


    /*
    |--------------------------------------------------------------------------
    | 13.Test Send Message
    |--------------------------------------------------------------------------
    */

    public function testSend(Request $request)
    {
        $devices        = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $templates      = $this->templateObserver->getData($request)->where('for_waba', 'no')->where('type', 'whatsapp')->get(['id', 'name']);
        return view('device.send_test', ['page'    => 'Kirim Pesan Single', 'breadcumb' => true], compact('devices', 'templates'));
    }


    /*
    |--------------------------------------------------------------------------
    | 14.Single Message Send
    |--------------------------------------------------------------------------
    */

    public function singleSend(Request $request)
    {
        $this->validate($request, [
            'device'            => 'required',
            'template'          => 'required',
            'type'              => 'required',
            'phone'             => 'required'
        ]);

        $settings       = Setting::first(['phone_country_code']);
        $phone          = $request->phone;

        if (substr($request->phone, 0, 1) == 0) {
            $phone      = $settings->phone_country_code . substr($request->phone, 1, 15);
        }

        $template       = MessageTemplate::find($request->template);
        $this->whatsappServiceObserver->sendMessage($phone, $request->device, $template->message, $template->media_data ?? '', $template->type_content, json_decode($template->button_or_list, true), $request->type == 'group' ? true : false);

        return redirect()->back()->with(['flash'    => 'Success Send Message']);
    }
}
