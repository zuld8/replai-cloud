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
        Schema::create('fine_tunnel_document_chunks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->text('content');
            $table->string('image_path')->nullable(); 
            $table->json('metadata')->nullable();
            $table->integer('chunk_index');
            $table->integer('token_count')->default(0); 
 
            $table->json('embedding')->nullable();

            $table->uuid('merchant_id')->nullable();
            $table->uuid('business_id')->nullable();
            $table->timestamps(); 
 
            $table->index(['document_id', 'chunk_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fine_tunnel_document_chunks');
    }
};
