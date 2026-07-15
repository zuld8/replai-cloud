<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Index untuk mempercepat query dashboard (HomeController) di:
 *  - history_chats      : 539K baris
 *  - history_chat_details: 2.6M baris
 *
 * Query yang dilayani:
 *  HC1: HistoryChat::whereBetween('created_at', [monthStart, monthEnd])->groupBy('status')
 *  HC2: HistoryChat::whereIn('status',['open','pending'])->orderBy('last_message_at','desc')
 *  HC3: HistoryChat::where('business_id',...)->whereBetween('created_at',...)
 *  HC4: YEARWEEK(created_at) + whereBetween('created_at') + merchant_id
 *  HC5: HistoryChat::where('business_id',...)->pluck('id') [label-leads]
 *  HCD1: details()->where('from','user')->orderBy('created_at','desc')->first()
 *  HCD2: details()->orderBy('created_at','desc')->first()
 */
return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        return !empty(DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$index]
        ));
    }

    private function addIndex(string $table, string $index, string $colsDef): void
    {
        if ($this->hasIndex($table, $index)) {
            echo "  [SKIP] {$table}.{$index} sudah ada\n";
            return;
        }
        DB::statement(
            "ALTER TABLE `{$table}` ADD INDEX `{$index}` {$colsDef}, ALGORITHM=INPLACE, LOCK=NONE"
        );
        echo "  [OK]   {$table}.{$index}\n";
    }

    public function up(): void
    {
        // ── history_chats ──────────────────────────────────────────
        // HC1: groupBy status + whereBetween created_at (interactions widget)
        $this->addIndex('history_chats', 'idx_hc_status_created',
            '(status, created_at)');

        // HC2: open/pending + order last_message_at (newest/oldest chats)
        $this->addIndex('history_chats', 'idx_hc_status_last_msg',
            '(status, last_message_at)');

        // HC3: business_id + created_at (label-leads, summary count per bisnis)
        $this->addIndex('history_chats', 'idx_hc_biz_created',
            '(business_id, created_at)');

        // HC4: merchant_id + created_at (interaction per merchant, YEARWEEK)
        $this->addIndex('history_chats', 'idx_hc_merchant_created',
            '(merchant_id, created_at)');

        // HC5: device_id + status (filter chat per device)
        $this->addIndex('history_chats', 'idx_hc_device_status',
            '(device_id, status)');

        // ── history_chat_details ──────────────────────────────────
        // HCD1: history_chat_id + from + created_at (last user message per chat)
        $this->addIndex('history_chat_details', 'idx_hcd_chat_from_created',
            '(history_chat_id, `from`, created_at)');

        // HCD2: history_chat_id + created_at (last message per chat, any sender)
        $this->addIndex('history_chat_details', 'idx_hcd_chat_created',
            '(history_chat_id, created_at)');
    }

    public function down(): void
    {
        $indexes = [
            'history_chats'        => ['idx_hc_status_created','idx_hc_status_last_msg','idx_hc_biz_created','idx_hc_merchant_created','idx_hc_device_status'],
            'history_chat_details' => ['idx_hcd_chat_from_created','idx_hcd_chat_created'],
        ];
        foreach ($indexes as $table => $list) {
            foreach ($list as $idx) {
                if ($this->hasIndex($table, $idx)) {
                    DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$idx}`");
                }
            }
        }
    }
};
