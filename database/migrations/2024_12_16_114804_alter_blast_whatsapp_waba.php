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
        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->enum('waba', ['yes', 'no'])->default('no')->after('delay');
            $table->string('meta_id')->nullable()->after('waba');
            $table->uuid('waba_id')->index()->nullable()->after('meta_id');
            $table->longText('metadata')->nullable()->after('waba_id');
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
