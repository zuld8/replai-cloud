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
        Schema::table('internal_settings', function (Blueprint $table) {
            $table->enum('method', ['bank', 'duitku'])->default('bank')->after('currency_position');
            $table->string('merchant_code')->nullable()->after('method');
            $table->string('api_key')->nullable()->after('merchant_code'); 
            $table->enum('is_production', ['yes', 'no'])->default('no');
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
