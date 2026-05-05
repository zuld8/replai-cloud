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
            $table->enum('schedule_frequency', ['once', 'daily', 'monthly', 'yearly'])->nullable()->after('whatsapp_sender_notif');
            $table->string('days')->nullable()->after('schedule_frequency');
            $table->string('month')->nullable()->after('days');
            $table->string('time')->nullable()->after('month');
            $table->string('end_date')->nullable()->after('time'); 
            $table->string('start_date')->nullable()->after('end_date'); 
            $table->string('yearly')->nullable()->after('start_date');
            $table->string('schedule')->nullable()->change();
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
