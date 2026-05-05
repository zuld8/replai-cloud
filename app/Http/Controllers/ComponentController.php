<?php

namespace App\Http\Controllers;

use App\Observers\Saas\InternalSettingObserver;
use App\Observers\WhatsappDeviceObserver;
use App\Observers\WhatsappOfficial\WhatsappOfficialObserver;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    protected $internalSettingObserver;
    protected $whatsappDeviceObserver;
    protected $officialObserver;

    public function __construct(InternalSettingObserver $internalSettingObserver, WhatsappDeviceObserver $whatsappDeviceObserver, WhatsappOfficialObserver $officialObserver)
    {
        $this->internalSettingObserver      = $internalSettingObserver;
        $this->whatsappDeviceObserver       = $whatsappDeviceObserver;
        $this->officialObserver             = $officialObserver;
    }

    public function system()
    {
        $setting    = $this->internalSettingObserver->generalSetting();
        return response()->json([
            'name'          => $setting->app_name,
            'app_name'      => config('app.name'),
            'server_url'    => config('custom.whatsapp_server_url'),
            'icon'          => asset($setting->icon),
            'logo'          => asset($setting->logo)
        ], 200);
    }

    public function getDevice(Request $request)
    {
        $devices    = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $officials  = $this->officialObserver->getData($request)->get(['id', 'meta_account_id', 'phone']);
        $data       = [];

        foreach ($devices as $device) {
            $item['id']     = $device->id;
            $item['name']   = $device->name;
            $item['phone']  = $device->phone;
            $item['type']   = 'Whatsapp Unofficial';
            $item['from']   = 'unofficial';
            $data[]         = $item;
        }

        foreach ($officials as $official) {
            $item['id']     = $official->id;
            $item['name']   = $official->meta->name ?? '';
            $item['phone']  = $official->phone;
            $item['type']   = 'Whatsapp Official';
            $item['from']   = 'waba';
            $data[]         = $item;
        }

        return response()->json($data);
    }
}
