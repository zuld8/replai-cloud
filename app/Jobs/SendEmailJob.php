<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Store\Store;

class SendEmailJob
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
        $schedulingPromotions   = BlashWhatsapp::where("use", "email")->where("status", "pending")->where("schedule", "<=", now())->orderBy('created_at', 'asc')->first();

        if ($schedulingPromotions) {
            $getStores          = Store::where(function ($q) use ($schedulingPromotions) {
                return $schedulingPromotions->category_id != null ? $q->where("category_id", $schedulingPromotions->category_id) : '';
            })->where(function ($q) use ($schedulingPromotions) {
                return $schedulingPromotions->district_id != null ? $q->where("district_id", $schedulingPromotions->district_id) : '';
            })->where(function ($q) use ($schedulingPromotions) {
                return $schedulingPromotions->city_id != null ? $q->whereHas("district", function ($q) use ($schedulingPromotions) {
                    return $q->where("city_id", $schedulingPromotions->city_id);
                }) : '';
            })->where("email", "!=", null)->where("status", "no")->orderBy('name', 'asc')->get();

            foreach ($getStores as $store) {

                BlashDetail::firstOrNew(
                    [
                        'blash_whatsapp_id'     =>  $schedulingPromotions->id,
                        'store_id'              => $store->id,
                    ],
                    [
                        'email'                 => $store->email,

                        'status'                => 'no'
                    ]
                )->save();
            }

            $schedulingPromotions->update([
                'status'        => 'success'
            ]);
        }
    }
}
