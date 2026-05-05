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
        Schema::create('pipeline_segments', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('business_id')->index()->nullable();
            $table->string('name');
            $table->string('color')->nullable();
            $table->integer('position')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_segments');
    }
};
