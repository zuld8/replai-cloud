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
        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->longText('agent')->nullable()->after('address');
        });

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->uuid('group_whatsapp_id')->nullable()->after('category_id');
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
