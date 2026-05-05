<?php

namespace App\Jobs;

use App\Models\ChatBot\FineTunnel;
use App\Models\Setting; 
use Illuminate\Support\Facades\Http;

class FineTunnelStatusCheckJob 
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
        $allFineTunning = FineTunnel::where(function ($q) {
            return $q->where("status", "processed")->orWhere("status", "pending")->orWhere("status", "processing");
        })->get(['id', 'status', 'fine_tunnel_id']);

        $settings   = Setting::first(['open_ai_key']);

        if ($settings->open_ai_key != null && $settings->open_ai_key != '') {
            foreach ($allFineTunning as $tun) {
                $getFile    = Http::withHeaders([
                    'Authorization' => "Bearer $settings->open_ai_key",
                ])->get('https://api.openai.com/v1/files/' . $tun->fine_tunnel_id);

                if ($getFile->status() == 200) {
                    $responseBody   = json_decode($getFile->body());
                    $tun->update([
                        'status'    => $responseBody->status
                    ]);
                }
            }
        }
    }
}
