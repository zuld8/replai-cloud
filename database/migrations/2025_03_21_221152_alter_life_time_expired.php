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
            $table->enum('days_option', ['limited', 'unlimited'])->after('add_days')->default('limited');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->enum('days_option', ['limited', 'unlimited'])->default('limited')->after('add_days');
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
