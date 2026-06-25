<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Single ALTER TABLE — more efficient than 3 separate statements on 303k-row table
        // Check each before adding to allow safe re-run
        $existing = collect(DB::select("SHOW INDEX FROM history_chats"))
            ->pluck('Key_name')->unique()->values()->toArray();

        $toAdd = [];
        if (!in_array('idx_telegram',  $existing)) $toAdd[] = 'ADD INDEX idx_telegram  (telegram_id)';
        if (!in_array('idx_instagram', $existing)) $toAdd[] = 'ADD INDEX idx_instagram (instagram_id)';
        if (!in_array('idx_messanger', $existing)) $toAdd[] = 'ADD INDEX idx_messanger (messanger_id)';

        if (!empty($toAdd)) {
            DB::statement('ALTER TABLE history_chats ' . implode(', ', $toAdd));
        }
    }
    public function down(): void {
        $existing = collect(DB::select("SHOW INDEX FROM history_chats"))
            ->pluck('Key_name')->unique()->toArray();
        $toDrop = [];
        foreach (['idx_telegram','idx_instagram','idx_messanger'] as $k) {
            if (in_array($k, $existing)) $toDrop[] = "DROP INDEX $k";
        }
        if (!empty($toDrop)) {
            DB::statement('ALTER TABLE history_chats ' . implode(', ', $toDrop));
        }
    }
};
