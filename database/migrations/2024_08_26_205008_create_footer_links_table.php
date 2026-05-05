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
        Schema::create('footer_links', function (Blueprint $table) {
            $table->uuid('id')->index()->nullable();
            $table->string('name');
            $table->string('url');
            $table->integer('position')->default(1);
            $table->integer('order_position')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
