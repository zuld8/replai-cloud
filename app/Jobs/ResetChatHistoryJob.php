<?php

namespace App\Jobs;

use App\Models\ChatBot\HistoryChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable; 

class ResetChatHistoryJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $histories   = HistoryChat::withoutGlobalScopes()->where("expire_date", "<=", date('Y-m-d'))->orderBy('created_at', 'asc')->get(); 
        foreach ($histories as $history) {
            $history->details()->delete();
            $history->delete();
        }
    }
}
