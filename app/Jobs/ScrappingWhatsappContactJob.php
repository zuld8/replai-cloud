<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\Setting;
use App\Models\Store\Scrapping;
use App\Models\WhatsappDevice;
use App\Observers\WhatsappServiceObserver; 
use Illuminate\Support\Facades\Log as FacadesLog;

class ScrappingWhatsappContactJob 
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
        $schedulingScrapping   = Scrapping::where("status", "pending")->where("schedule", "<=", now())->where('scrapping_method', 'contacts')->orderBy('created_at', 'asc')->first();

        if (!$schedulingScrapping) { 
            return;
        }
        
        if ($schedulingScrapping) {


            $log = Log::create([
                'description'   => __('scrapp.start_scrapping', [
                    'name'          => $schedulingScrapping->name
                ]),
                'type'          => 'scrapping',
                'merchant_id'   => $schedulingScrapping->merchant_id ?? null,
                'business_id'   => $schedulingScrapping->business_id ?? null
            ]);

            $setting    = Setting::where("id", $schedulingScrapping->business_id)->first(['scrapp_counter']);

            if ($schedulingScrapping->business_id != null) {

                $merchant   = $schedulingScrapping->merchant ?? null;
                if ($setting != null && $merchant != null) {
                    $transaction = $merchant->package_active;
                    if (!$transaction) {

                        $schedulingScrapping->update([
                            'status'        => 'success',
                        ]);

                        $log->update([
                            'status'        => 'error',
                            'error'         => 'Paket Langganan telah Berakhir'
                        ]);
                    }

                    if ($transaction->limit_scrapp_option == 'yes') {
                        if ($setting->scrapp_counter >= $transaction->scrapp_limit) {
                            $schedulingScrapping->update([
                                'status'        => 'success'
                            ]);

                            $log->update([
                                'status'        => 'error',
                                'error'         => 'Limit Scrapping harian telah habis'
                            ]);
                        }
                    }
                }
            }


            try {
                $whatsappServiceObserver    = new WhatsappServiceObserver();
                // N+1 FIX: pre-load semua devices sekaligus (1 query)
                $deviceIds = array_filter(explode(",", $schedulingScrapping->devices));
                $devicesMap = WhatsappDevice::whereIn('id', $deviceIds)->get()->keyBy('id');
                foreach ($deviceIds as $device) {
                    FacadesLog::info($device . ' - Ada');
                    $device = $devicesMap->get($device);
                    if ($device) {
                        FacadesLog::info($device->id . ' - persiapan kirim');
                        if ($device->status == 'active') {

                            $result = $whatsappServiceObserver->getContactDevices($device->id, $schedulingScrapping->id);
                            FacadesLog::info($result->status());
                        }
                    }
                }

                $schedulingScrapping->update([
                    'status'        => 'success'
                ]);

                $log->update([
                    'status'    => 'success'
                ]);
            } catch (\Exception $e) {

                $schedulingScrapping->update([
                    'status'        => 'success',
                ]);

                $log->update([
                    'error'     => $e->getMessage(),
                    'status'    => 'error'
                ]);
            }
        }
    }
}
