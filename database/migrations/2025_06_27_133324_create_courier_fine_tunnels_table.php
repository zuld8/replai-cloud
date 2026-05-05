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
        Schema::create('courier_fine_tunnels', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('finetunnel_id')->index();
            $table->string('name');
            $table->string('code');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_fine_tunnels');
    }
};
