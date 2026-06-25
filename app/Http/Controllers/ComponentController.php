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
        $data = [];

        // 1. WhatsApp Unofficial (Baileys) — pakai observer biar kena FilterByBusinessScope
        $devices = $this->whatsappDeviceObserver->getData($request)
            ->where('status', 'active')
            ->get(['id', 'name', 'phone']);
        foreach ($devices as $device) {
            $data[] = [
                'id'    => $device->id,
                'name'  => $device->name ?: $device->phone,
                'phone' => $device->phone,
                'type'  => 'WhatsApp',
                'from'  => 'unofficial',
            ];
        }

        // 2. WhatsApp Business (WABA) — pakai observer biar kena FilterByBusinessScope
        $officials = $this->officialObserver->getData($request)->get(['id', 'meta_account_id', 'phone']);
        foreach ($officials as $official) {
            $data[] = [
                'id'    => $official->id,
                'name'  => $official->meta->name ?? ($official->phone ?: 'WABA'),
                'phone' => $official->phone,
                'type'  => 'WhatsApp Business',
                'from'  => 'waba',
            ];
        }

        // 3. Telegram
        $telegrams = \App\Models\TelegramKey::where('business_id', my_business())->get(['id', 'name']);
        foreach ($telegrams as $t) {
            $data[] = ['id' => $t->id, 'name' => $t->name ?: 'Telegram', 'phone' => '', 'type' => 'Telegram', 'from' => 'telegram'];
        }

        // 4. Instagram
        $instas = \App\Models\Meta\InstagramAccount::where('business_id', my_business())->get(['id', 'name', 'username']);
        foreach ($instas as $i) {
            $data[] = ['id' => $i->id, 'name' => ($i->name ?: $i->username) ?: 'Instagram', 'phone' => '@' . $i->username, 'type' => 'Instagram', 'from' => 'instagram'];
        }

        // 5. Messenger
        $messengers = \App\Models\Meta\MessengerAccount::where('business_id', my_business())->get(['id', 'page_name', 'page_username']);
        foreach ($messengers as $m) {
            $data[] = ['id' => $m->id, 'name' => $m->page_name ?: 'Messenger', 'phone' => $m->page_username ?? '', 'type' => 'Messenger', 'from' => 'messanger'];
        }

        // 6. Live Chat
        $livechats = \App\Models\LiveChat::where('business_id', my_business())->get(['id', 'name']);
        foreach ($livechats as $l) {
            $data[] = ['id' => $l->id, 'name' => $l->name ?: 'Live Chat', 'phone' => 'Web', 'type' => 'Live Chat', 'from' => 'livechat'];
        }

        return response()->json($data);
    }
}
