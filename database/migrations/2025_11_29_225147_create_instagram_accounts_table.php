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
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('business_id')->index();
            $table->text('agent')->nullable(); 
            $table->string('instagram_id')->comment('Instagram Business Account ID');
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->text('profile_picture_url')->nullable();
            $table->string('biography')->nullable();
            $table->string('website')->nullable();
            $table->integer('followers_count')->default(0);
            $table->integer('follows_count')->default(0);
            $table->integer('media_count')->default(0); 
            $table->string('page_id')->nullable()->comment('Connected Facebook Page ID');
            $table->string('page_name')->nullable(); 
            $table->text('access_token')->comment('Long-lived access token');
            $table->timestamp('token_expires_at')->nullable(); 
            $table->enum('status', ['active', 'inactive', 'expired', 'error'])->default('active');
            $table->text('error_message')->nullable(); 
            $table->json('details')->nullable()->comment('Full Instagram account details'); 
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};
