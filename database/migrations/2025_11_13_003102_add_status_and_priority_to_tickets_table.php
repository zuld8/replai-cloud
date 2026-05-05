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
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open')->after('ticket_id');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low')->after('status');
            $table->uuid('resolved_by')->nullable()->after('notes');
            $table->datetime('resolved_at')->nullable()->after('resolved_by');
            $table->datetime('assigned_at')->nullable()->after('resolved_at');
            
            // Add indexes for better query performance
            $table->index('status');
            $table->index('priority');
            $table->index('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['status', 'priority', 'resolved_by', 'resolved_at', 'assigned_at']);
        });
    }
};
