<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;

class ResetWhatsappDailySendJob
{



    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $whatsappAccount    = WhatsappKeyAccount::withoutGlobalScopes()->get(['id', 'daily_send']);
        $whatsappDevice     = WhatsappDevice::withoutGlobalScopes()->get(['id', 'daily_send']);
        $merchantsSett      = Setting::withoutGlobalScopes()->get(['id', 'scrapp_counter', 'whatsapp_sender', 'email_sender']);

        foreach ($merchantsSett as $sett) {
            $sett->update([
                'scrapp_counter'        => 0,
                'whatsapp_sender'       => 0,
                'email_sender'          => 0
            ]);
        }

        foreach ($whatsappAccount as $whatsapp) {
            $whatsapp->update([
                'daily_send'    => 0
            ]);
        }

        foreach ($whatsappDevice as $device) {
            $device->update([
                'daily_send'    => 0
            ]);
        }
    }
}
