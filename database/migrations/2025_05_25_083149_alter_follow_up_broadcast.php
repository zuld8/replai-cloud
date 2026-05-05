<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // DB::statement("ALTER TABLE blash_whatsapps MODIFY COLUMN `use` ENUM('whatsapp', 'email', 'whatsapp_follow_up') default 'whatsapp'");

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->string('use')->default('whatsapp')->change();
        });

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->uuid('category_id')->nullable()->change();
            $table->uuid('template_id')->nullable()->change();
            $table->text('labels')->nullable()->after('file');
            $table->enum('just_for_no_reply', ['yes', 'no'])->default('no')->after('labels');
            $table->integer('delay_for_not_reply')->default(0)->after('just_for_no_reply');
            $table->enum('delay_message_option', ['yes', 'no'])->default('no')->after('delay_for_not_reply');
            $table->integer('delay_message')->default(0)->after('delay_message_option');
            $table->text('ai_prompt')->nullable()->after('delay_message');
            $table->enum('broadcast_method', ['template', 'ai'])->default('template')->after('ai_prompt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
