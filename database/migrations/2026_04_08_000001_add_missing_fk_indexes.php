<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ticket_agents indexes
        if (Schema::hasTable('ticket_agents')) {
            Schema::table('ticket_agents', function (Blueprint $table) {
                if (!$this->indexExists('ticket_agents', 'ticket_agents_agent_id_index')) {
                    $table->index('agent_id', 'ticket_agents_agent_id_index');
                }
                if (!$this->indexExists('ticket_agents', 'ticket_agents_ticket_id_index')) {
                    $table->index('ticket_id', 'ticket_agents_ticket_id_index');
                }
            });
        }

        // ticket_notes indexes
        if (Schema::hasTable('ticket_notes')) {
            Schema::table('ticket_notes', function (Blueprint $table) {
                if (!$this->indexExists('ticket_notes', 'ticket_notes_ticket_id_index')) {
                    $table->index('ticket_id', 'ticket_notes_ticket_id_index');
                }
                if (!$this->indexExists('ticket_notes', 'ticket_notes_user_id_index')) {
                    $table->index('user_id', 'ticket_notes_user_id_index');
                }
            });
        }

        // messenger_accounts indexes
        if (Schema::hasTable('messenger_accounts')) {
            Schema::table('messenger_accounts', function (Blueprint $table) {
                if (!$this->indexExists('messenger_accounts', 'messenger_accounts_business_id_index')) {
                    $table->index('business_id', 'messenger_accounts_business_id_index');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('ticket_agents', function (Blueprint $table) {
            $table->dropIndexIfExists('ticket_agents_agent_id_index');
            $table->dropIndexIfExists('ticket_agents_ticket_id_index');
        });
        Schema::table('ticket_notes', function (Blueprint $table) {
            $table->dropIndexIfExists('ticket_notes_ticket_id_index');
            $table->dropIndexIfExists('ticket_notes_user_id_index');
        });
        Schema::table('messenger_accounts', function (Blueprint $table) {
            $table->dropIndexIfExists('messenger_accounts_business_id_index');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return count($indexes) > 0;
    }
};
