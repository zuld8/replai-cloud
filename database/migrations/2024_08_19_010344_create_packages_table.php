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
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('name');
            $table->decimal('price', 22, 4)->default(0);
            $table->float('add_days')->default(1);
            $table->enum('trial_version', ['yes', 'no'])->default('no');
            $table->enum('limit_user_option', ['yes', 'no'])->default('yes');
            $table->float('users_limit')->default(1);
            $table->enum('limit_whatsapp_option', ['yes', 'no'])->default('yes');
            $table->enum('limit_whatsapp_priode', ['monthly', 'daily', 'yearly'])->default('monthly');
            $table->float('whatsapp_limit')->default(1000);
            $table->enum('limit_email_option', ['yes', 'no'])->default('yes');
            $table->enum('limit_email_priode', ['monthly', 'daily', 'yearly'])->default('monthly');
            $table->float('email_limit')->default(1000);
            $table->enum('limit_scrapp_option', ['yes', 'no'])->default('yes');
            $table->enum('limit_scrapp_priode', ['monthly', 'daily', 'yearly'])->default('monthly');
            $table->float('scrapp_limit')->default(1000);
            $table->enum('limit_device', ['yes', 'no'])->default('yes');
            $table->float('device_limit')->default(1);
            $table->enum('limit_template', ['yes', 'no'])->default('yes');
            $table->float('template_limit')->default(1);
            $table->enum('limit_ai_training', ['yes', 'no'])->default('yes');
            $table->float('ai_training_limit')->default(1);
            $table->enum('limit_chatbot', ['yes', 'no'])->default('yes');
            $table->float('chatbot_limit')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
