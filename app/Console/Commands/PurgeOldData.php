<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurgeOldData extends Command
{
    protected $signature = "crm:purge-old-data
                            {--months=6 : Delete data older than N months}
                            {--dry-run : Show counts without deleting}
                            {--force : Skip confirmation prompt}";

    protected $description = "Purge old history_chat_details, broadcast logs, and soft-deleted rows";

    public function handle(): int
    {
        $months = (int) $this->option("months");
        $dryRun = $this->option("dry-run");
        $force  = $this->option("force");
        $cutoff = Carbon::now()->subMonths($months);

        $this->info("Purge cutoff: {$cutoff->toDateTimeString()} (--months={$months})");
        if ($dryRun) $this->warn("DRY-RUN mode -- no data will be deleted.");

        // Count rows
        $countDetails = DB::table("history_chat_details")
            ->whereNotNull("deleted_at")
            ->where("deleted_at", "<", $cutoff)
            ->count();
        $this->line("  history_chat_details (soft-deleted, old): {$countDetails} rows");

        $countOldDetails = DB::table("history_chat_details")
            ->whereNull("deleted_at")
            ->where("created_at", "<", $cutoff)
            ->count();
        $this->line("  history_chat_details (old, not deleted): {$countOldDetails} rows");

        $countChats = DB::table("history_chats")
            ->whereNotNull("deleted_at")
            ->where("deleted_at", "<", $cutoff)
            ->count();
        $this->line("  history_chats (soft-deleted, old): {$countChats} rows");

        $countLogs = DB::table("logs")
            ->where("created_at", "<", $cutoff)
            ->count();
        $this->line("  logs (old): {$countLogs} rows");

        $total = $countDetails + $countChats + $countLogs;
        $this->info("Total rows to purge: {$total}");

        if ($dryRun) {
            $this->warn("DRY-RUN complete. Run without --dry-run to actually delete.");
            return 0;
        }

        if (! $force && ! $this->confirm("Proceed with deletion?")) {
            $this->line("Aborted.");
            return 0;
        }

        // Execute purge
        $deleted1 = DB::table("history_chat_details")
            ->whereNotNull("deleted_at")
            ->where("deleted_at", "<", $cutoff)
            ->delete();
        $this->line("  Deleted {$deleted1} soft-deleted details");

        $deleted2 = DB::table("history_chats")
            ->whereNotNull("deleted_at")
            ->where("deleted_at", "<", $cutoff)
            ->delete();
        $this->line("  Deleted {$deleted2} soft-deleted chats");

        $deleted3 = DB::table("logs")
            ->where("created_at", "<", $cutoff)
            ->delete();
        $this->line("  Deleted {$deleted3} old logs");

        // Optimize tables
        $this->line("  Optimizing tables (may take a few minutes)...");
        DB::statement("OPTIMIZE TABLE history_chat_details, history_chats, logs");

        $this->info("Purge complete! Tables optimized.");
        return 0;
    }
}
