<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'sending' state to blash_details.sending_status enum.
     * This enables the 3-state atomic claim pattern:
     *   no -> sending (claimed) -> yes (confirmed sent)
     * Preventing double-send on job retry.
     */
    public function up(): void
    {
        // MySQL ALTER TABLE ... MODIFY COLUMN is safe for enum extension
        DB::statement("
            ALTER TABLE blash_details
            MODIFY COLUMN sending_status
            ENUM('yes','no','sending') NOT NULL DEFAULT 'no'
        ");
    }

    public function down(): void
    {
        // First reset any stuck 'sending' to 'no' before reverting enum
        DB::statement("UPDATE blash_details SET sending_status = 'no' WHERE sending_status = 'sending'");
        DB::statement("
            ALTER TABLE blash_details
            MODIFY COLUMN sending_status
            ENUM('yes','no') NOT NULL DEFAULT 'no'
        ");
    }
};
