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
            $table->decimal('price_token', 22, 4)->default(0)->after('currency_position');
            $table->decimal('token_per_price', 22, 4)->default(0)->after('price_token');
            $table->decimal('credit_token_basic', 22, 4)->default(0)->after('token_per_price');
            $table->decimal('credit_token_advance', 22, 4)->default(0)->after('credit_token_basic');
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
