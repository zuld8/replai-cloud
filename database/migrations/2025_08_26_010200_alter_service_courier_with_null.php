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
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('service')->nullable()->change();
        });

         Schema::table('courier_fine_tunnels', function (Blueprint $table) {
            $table->string('service')->nullable()->change();
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
