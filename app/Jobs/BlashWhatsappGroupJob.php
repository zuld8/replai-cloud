<?php

namespace App\Jobs;

use App\Models\Blash\BlashDetail;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Store\WhatsappGroup;
use App\Services\Sistem\QueueRouter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class BlashWhatsappGroupJob implements ShouldQueue
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
        $schedules   = BlashWhatsapp::where("use", "whatsapp_group")->where("status", "pending")->where("schedule", "<=", now())->orderBy('created_at', 'asc')->get();

        if (count($schedules) == 0) {
            return; // Keluar dari job
        }

        foreach ($schedules as $schedulingPromotions) {
            $delay              = $schedulingPromotions->delay;
            $getGroups          = WhatsappGroup::where(function ($q) use ($schedulingPromotions) {
                return  $q->whereIn("device_id", explode(",", $schedulingPromotions->devices));
            })->whereIn("id", explode(",", $schedulingPromotions->groups))->orderBy('name', 'asc')->get();

            $lastShceduleOn    = BlashDetail::where("status", "no")->whereHas('parent', function ($q) use ($schedulingPromotions) {
                return $q->where("use", 'whatsapp_group');
            })->whereHas('parent', function ($q) use ($schedulingPromotions) {
                $q->where('business_id', $schedulingPromotions->business_id)->orWhere('business_id', null);
            })->orderBy('schedule', 'desc')->first(['schedule']);

            $last   = now();
            $number = 0;
            if ($lastShceduleOn) {
                if ($lastShceduleOn->schedule != null) {
                    $parsedSchedule = Carbon::parse($lastShceduleOn->schedule);

                    if ($parsedSchedule->greaterThan(now())) {
                        $last = $parsedSchedule;
                    }
                }
            }

            foreach ($getGroups as $group) {
                $schedule       = (clone $last)->addSeconds($number++ * $delay);


                $message = BlashDetail::firstOrCreate(
                    [
                        'blash_whatsapp_id' => $schedulingPromotions->id,
                        'whatsapp_group_id' => $group->id
                    ],
                    [
                        'phone'     => $group->group_id,
                        'status'    => 'no',
                        'schedule'  => $schedule->format('Y-m-d H:i:s')
                    ]
                );

                $queue = QueueRouter::getQueue($schedulingPromotions->business_id, 'broadcast');
                dispatch(new SendWhatsappGroupJob($message))
                    ->onQueue($queue)
                    ->delay($schedule);
                    
            }

            $schedulingPromotions->update([
                'status'        => 'success'
            ]);
        }
    }
}
