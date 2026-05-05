<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\Store\WhatsappGroup;
use App\Observers\WhatsappServiceObserver; 

class ScrappingContactOnGroupJob  
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
        $schedulingScrapping   = WhatsappGroup::where("scraping", "yes")->orderBy('created_at', 'asc')->first();


        if ($schedulingScrapping) {

            $setting    = Setting::where("id", $schedulingScrapping->business_id)->first(['scrapp_counter']);

            if ($schedulingScrapping->business_id != null) {

                $merchant   = $schedulingScrapping->merchant ?? null;
                if ($setting != null && $merchant != null) {
                    $transaction = $merchant->package_active;
                    if (!$transaction) {

                        $schedulingScrapping->update([
                            'scraping'        => 'no',
                        ]);
                    }

                    if ($transaction->limit_scrapp_option == 'yes') {
                        if ($setting->scrapp_counter >= $transaction->scrapp_limit) {
                            $schedulingScrapping->update([
                                'scraping'        => 'no'
                            ]);
                        }
                    }
                }
            }


            try {
                $whatsappServiceObserver    = new WhatsappServiceObserver();
                $device         = $schedulingScrapping->device;
                if ($device) {
                    if ($device->status == 'active') {
                        $whatsappServiceObserver->getGroupContact($device->id, $schedulingScrapping->group_id, $schedulingScrapping->business_id);
                    }
                }

                $schedulingScrapping->update([
                    'scraping'        => 'no'
                ]);
            } catch (\Exception $e) {

                $schedulingScrapping->update([
                    'scraping'        => 'no',
                ]);
            }
        }
    }
}
