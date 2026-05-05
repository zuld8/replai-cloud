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
        Schema::create('messenger_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('business_id');
            $table->string('page_id')->index();
            $table->string('agent')->nullable();
            $table->string('page_name');
            $table->string('page_username')->nullable();
            $table->text('page_picture_url')->nullable();
            $table->string('category')->nullable();
            $table->text('about')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('followers_count')->default(0);
            $table->string('access_token', 500);
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'error'])->default('active');
            $table->text('error_message')->nullable();
            $table->json('details')->nullable();

            // Auto Reply Settings
            $table->enum('auto_reply_method', ['chatbot', 'ai', 'all', 'none'])->default('none');
            $table->uuid('fine_tunnel_id')->nullable();

            // Daily Limit
            $table->enum('daily_limit', ['yes', 'no'])->default('no');
            $table->integer('limit_per_day')->default(0);
            $table->integer('daily_send')->default(0);
            $table->date('daily_date')->nullable();

            // Time-based Auto Reply
            $table->enum('auto_reply_certain_day', ['yes', 'no'])->default('no');
            $table->string('days')->nullable(); // Mon,Tue,Wed,Thu,Fri,Sat,Sun
            $table->enum('auto_reply_certain_time', ['yes', 'no'])->default('no');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('fine_tunnel_id')->references('id')->on('fine_tunnels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messanger_accounts');
    }
};
