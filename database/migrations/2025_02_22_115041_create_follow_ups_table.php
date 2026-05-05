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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('finetunnel_id')->index();
            $table->text('text')->nullable();
            $table->integer('delay')->default(5);
            $table->enum('exact', ['yes', 'no'])->default('no');
            $table->enum('handoff', ['yes', 'no'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
