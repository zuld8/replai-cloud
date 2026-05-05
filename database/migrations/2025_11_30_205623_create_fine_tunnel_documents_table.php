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
        Schema::create('fine_tunnel_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fine_tunnel_id');
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_type'); 
            $table->integer('file_size');
            $table->integer('total_chunks')->default(0);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->text('error_message')->nullable();
            $table->uuid('merchant_id')->nullable();
            $table->uuid('business_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fine_tunnel_documents');
    }
};
