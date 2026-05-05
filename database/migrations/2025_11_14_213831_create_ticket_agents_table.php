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
        Schema::create('ticket_agents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id')->index();
            $table->uuid('agent_id')->index();
            $table->string('role')->nullable()->default('assigned'); // assigned, primary, support
            $table->timestamp('assigned_at')->useCurrent();
            
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent duplicate assignments
            $table->unique(['ticket_id', 'agent_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_agents');
    }
};
