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
            $table->enum('option_audio_to_text_ai', ['yes', 'no'])->default('no')->after('merchant_id'); 
            $table->integer('min_audio')->default(0)->after('option_audio_to_text_ai');
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
