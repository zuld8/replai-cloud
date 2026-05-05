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
             $table->string('footer_1')->default('Footer Link 1')->after('web_template'); 
             $table->string('footer_2')->default('Footer Link 2')->after('footer_1'); 
             $table->string('footer_3')->default('Footer Link 3')->after('footer_2'); 
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
