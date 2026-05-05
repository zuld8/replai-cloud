<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            $table->uuid('device_id')->nullable()->change();
            $table->uuid('livechat_id')->index()->nullable()->after('device_id');
            $table->string('from')->default('whatsapp')->after('expire_date');
            $table->enum('status', ['resolved', 'open', 'block', 'pending'])->default('open')->after('from');
            $table->text('label')->nullable()->after('status');
            $table->text('note')->nullable()->after('label');
            $table->uuid('handled_by')->index()->nullable()->after('note');
            $table->text('collabolator')->nullable()->after('handled_by');
            $table->enum('takeover', ['yes', 'no'])->default('no')->after('collabolator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('and_label', function (Blueprint $table) {
            //
        });
    }
};
