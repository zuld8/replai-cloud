<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\Setting;
use App\Models\Store\Scrapping;
use App\Observers\Store\StoreScrappingObserver;
use Illuminate\Support\Facades\Log as FacadesLog;

class ScrappingGmapsJob
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
        $schedulingScrapping   = Scrapping::where("status", "pending")->where("schedule", "<=", now())->where('scrapping_method', 'gmaps')->orderBy('created_at', 'asc')->first();

        if (!$schedulingScrapping) {
            return; // Keluar dari job
        }

        FacadesLog::info("masuk nih bos -" . isset($schedulingScrapping) ? 'ada' : 'ga ada');
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

            FacadesLog::info("masuk nih bos");
            if ($schedulingScrapping->business_id != null) {

                FacadesLog::info("masuk nih bos");
                $merchant   = $schedulingScrapping->merchant ?? null;
                if ($setting != null && $merchant != null) {

                    FacadesLog::info("malah kesini");
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

                FacadesLog::info("masuk nih bos");
                $scrappingObserver = new StoreScrappingObserver();
                $scrappingProcess = $scrappingObserver->scrappingData($schedulingScrapping);
                FacadesLog::info("ada");
                if ($scrappingProcess == true) {
                    $schedulingScrapping->update([
                        'status'        => 'success'
                    ]);

                    $log->update([
                        'status'    => 'success'
                    ]);

                    FacadesLog::info("berhasil");
                } else {

                    $schedulingScrapping->update([
                        'status'        => 'success'
                    ]);

                    $log->update([
                        'error'     => __('scrapp.error_api_key'),
                        'status'    => 'error'
                    ]);

                    FacadesLog::info("gagal");
                }
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
