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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('contact_id')->index();
            $table->uuid('label_id')->index();
            $table->uuid('agent_id')->index();
            $table->string('ticket_name');
            $table->enum('ticket_level', ['low', 'medium', 'high'])->default('low');
            $table->uuid('category_id')->index();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->string('file')->nullable();
            $table->string('ticket_id')->unique();            
            $table->dateTime('create_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
