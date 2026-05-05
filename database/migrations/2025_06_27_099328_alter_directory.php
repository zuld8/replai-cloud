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
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn('merchant_id');
            $table->dropColumn('business_id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('merchant_id');
            $table->dropColumn('business_id');
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
