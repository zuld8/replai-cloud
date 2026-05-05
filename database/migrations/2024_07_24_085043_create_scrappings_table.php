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
        Schema::create('scrappings', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('category_id')->index()->nullable(); 
            $table->uuid('district_id')->index()->nullable();
            $table->string('name');
            $table->dateTime('schedule')->nullable();
            $table->enum('status', ['pending', 'success'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrappings');
    }
};
