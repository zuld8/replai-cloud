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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('max_per_upload')->default(2)->after('google_sheet');
            $table->integer('max_total_rag')->default(5)->after('max_per_upload'); 
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->integer('max_per_upload')->default(2)->after('google_sheet');
            $table->integer('max_total_rag')->default(5)->after('max_per_upload'); 
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
