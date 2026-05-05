<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResetTokenAiJob implements ShouldQueue
{
    use Queueable;

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
        $business   = Setting::whereHas('package_active', function ($q) {
            return $q->where('days_option', '!=', 'unlimited');
        })->limit(200)->get(['id']);

        foreach ($business as $bisnis) {
            $usingPackage   = $bisnis->package_active ? (int)$bisnis->package_active->using_credit_limit : 0;
            $packageCredit  = $bisnis->package_active ? (int)$bisnis->package_active->new_order_ai_response : 0;

            $bisnis->package_active->update([
                'using_credit_limit'        => max(0, $usingPackage - $packageCredit)
            ]);
        }
    }
}
