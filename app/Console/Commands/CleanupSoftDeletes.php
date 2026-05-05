<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupSoftDeletes extends Command
{
    protected $signature = 'cleanup:soft-deletes {--days=30 : Days to keep soft-deleted records}';
    protected $description = 'Permanently delete soft-deleted records older than N days';

    // Tables with deleted_at column
    private $tables = [
        'meta_accounts',
        'instagram_accounts',
        'messenger_accounts',
        'merchants',
        'scrappings',
        'users',
        'packages',
    ];

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days)->toDateTimeString();
        $totalDeleted = 0;

        $this->info("Cleaning soft-deleted records older than {$days} days (before {$cutoff})");

        foreach ($this->tables as $table) {
            try {
                $count = DB::table($table)
                    ->whereNotNull('deleted_at')
                    ->where('deleted_at', '<', $cutoff)
                    ->count();

                if ($count > 0) {
                    DB::table($table)
                        ->whereNotNull('deleted_at')
                        ->where('deleted_at', '<', $cutoff)
                        ->delete();

                    $this->info("  {$table}: {$count} records permanently deleted");
                    $totalDeleted += $count;
                }
            } catch (\Exception $e) {
                $this->warn("  {$table}: Error - {$e->getMessage()}");
                Log::warning("CleanupSoftDeletes error on {$table}: {$e->getMessage()}");
            }
        }

        if ($totalDeleted === 0) {
            $this->info("  No records to clean up.");
        } else {
            $this->info("Total: {$totalDeleted} records permanently deleted.");
            Log::info("CleanupSoftDeletes: {$totalDeleted} records removed.");
        }

        return Command::SUCCESS;
    }
}
