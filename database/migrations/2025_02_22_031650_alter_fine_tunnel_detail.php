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
        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->string('welcome_image')->nullable()->after('stop_ai_handoff');
            $table->longText('term_condition')->nullable()->after('welcome_image');
            $table->enum('model_ai', ['advanced', 'standart'])->default('standart')->after('term_condition');
            $table->integer('history_limit')->default(20)->after('model_ai');
            $table->text('label')->nullable()->after('history_limit');
            $table->integer('context_limit')->default(10)->after('label');
            $table->integer('delay')->default(10)->after('context_limit');
            $table->integer('message_limit')->default(1000)->after('delay');
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
