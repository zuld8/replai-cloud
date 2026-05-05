<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('scrappings', function (Blueprint $table) {
            $table->string('scrapping_method')->default('gmaps')->change();
        });

        // DB::statement("ALTER TABLE scrappings MODIFY COLUMN scrapping_method ENUM('group', 'gmaps', 'contacts')"); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
