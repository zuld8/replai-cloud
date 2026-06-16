<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetStuckSendingRecords extends Command
{
    protected $signature = 'broadcast:reset-stuck
                            {--minutes=45 : Reset records stuck in "sending" state for longer than N minutes (45min > timeout+retry buffer)}
                            {--dry-run : Show count without updating}';

    protected $description = 'Reset blash_details records stuck in sending_status="sending" (worker died mid-flight)';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $dryRun  = $this->option('dry-run');
        $cutoff  = now()->subMinutes($minutes);

        $count = DB::table('blash_details')
            ->where('sending_status', 'sending')
            ->where('sending', '<', $cutoff)
            ->count();

        $this->line("Records stuck in 'sending' older than {$minutes}min: <info>{$count}</info>");

        if ($count === 0) {
            $this->info('Nothing to reset.');
            return 0;
        }

        if ($dryRun) {
            $this->warn('DRY-RUN — no changes made.');
            return 0;
        }

        $updated = DB::table('blash_details')
            ->where('sending_status', 'sending')
            ->where('sending', '<', $cutoff)
            ->update([
                'sending_status' => 'no',
                'reports'        => 'Reset from stuck "sending" state by recovery command',
                'updated_at'     => now(),
            ]);

        $this->info("Reset {$updated} stuck records back to 'no'. They will be retried.");

        Log::info("broadcast:reset-stuck: reset {$updated} records older than {$minutes}min");

        return 0;
    }
}
