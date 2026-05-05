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
            $table->string('welcome_message')->nullable()->after('min_audio');
            $table->string('transfer_condition')->nullable()->after('welcome_message');
            $table->enum('stop_ai_handoff',['yes','no'])->default('no')->after('transfer_condition');
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
