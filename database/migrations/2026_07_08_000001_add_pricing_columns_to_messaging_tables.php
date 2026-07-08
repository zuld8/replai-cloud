<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blash_details', function (Blueprint $table) {
            if (!Schema::hasColumn('blash_details', 'msg_category'))
                $table->string('msg_category', 20)->nullable()->comment('Meta category: marketing/utility/authentication/service');
            if (!Schema::hasColumn('blash_details', 'billable'))
                $table->boolean('billable')->nullable()->comment('true=ditagih Meta');
            if (!Schema::hasColumn('blash_details', 'pricing_model'))
                $table->string('pricing_model', 20)->nullable()->comment('PMP=per-message');
            if (!Schema::hasColumn('blash_details', 'conversation_id'))
                $table->string('conversation_id', 80)->nullable();
        });

        Schema::table('history_chat_details', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chat_details', 'msg_category'))
                $table->string('msg_category', 20)->nullable()->comment('Meta category: marketing/utility/authentication/service');
            if (!Schema::hasColumn('history_chat_details', 'billable'))
                $table->boolean('billable')->nullable()->comment('true=ditagih Meta');
            if (!Schema::hasColumn('history_chat_details', 'pricing_model'))
                $table->string('pricing_model', 20)->nullable();
            if (!Schema::hasColumn('history_chat_details', 'conversation_id'))
                $table->string('conversation_id', 80)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('blash_details', function (Blueprint $table) {
            foreach (['msg_category','billable','pricing_model','conversation_id'] as $col) {
                if (Schema::hasColumn('blash_details', $col)) $table->dropColumn($col);
            }
        });
        Schema::table('history_chat_details', function (Blueprint $table) {
            foreach (['msg_category','billable','pricing_model','conversation_id'] as $col) {
                if (Schema::hasColumn('history_chat_details', $col)) $table->dropColumn($col);
            }
        });
    }
};
