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
        Schema::table('scrappings', function (Blueprint $table) {
            $table->enum('scrapping_method',['gmaps','group'])->default('gmaps');
            $table->longText('devices')->nullable()->after('scrapping_method');
            $table->uuid('district_id')->nullable()->change();
            $table->uuid('category_id')->nullable()->change();
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
