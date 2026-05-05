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
            $table->enum('footer_web', ['yes', 'no'])->default('yes')->after('contact_address');
            $table->string('web_template')->default('template2')->after('footer_web');
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
