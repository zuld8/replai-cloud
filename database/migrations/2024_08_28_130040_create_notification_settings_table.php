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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('received_notification')->nullable();
            $table->string('received_email_notification')->nullable();
            $table->uuid('device_notification')->nullable();
            $table->enum('whatsapp_register', ['yes', 'no'])->default('no');
            $table->uuid('whatsapp_register_template')->nullable();
            $table->enum('whatsapp_buy_package', ['yes', 'no'])->default('no');
            $table->uuid('whatsapp_buy_package_template')->nullable();
            $table->enum('whatsapp_package_payment', ['yes', 'no'])->default('no');
            $table->uuid('whatsapp_package_payment_template')->nullable();
            $table->enum('whatsapp_package_user', ['yes', 'no'])->default('no');
            $table->uuid('whatsapp_package_user_template')->nullable();
            $table->enum('whatsapp_approval_payment', ['yes', 'no'])->default('no');
            $table->uuid('whatsapp_approval_payment_template')->nullable();
            $table->enum('email_register', ['yes', 'no'])->default('no');
            $table->uuid('email_register_template')->nullable();
            $table->enum('email_buy_package', ['yes', 'no'])->default('no');
            $table->uuid('email_buy_package_template')->nullable();
            $table->enum('email_package_payment', ['yes', 'no'])->default('no');
            $table->uuid('email_package_payment_template')->nullable();
            $table->enum('email_package_user', ['yes', 'no'])->default('no');
            $table->uuid('email_package_user_template')->nullable();
            $table->enum('email_approval_payment', ['yes', 'no'])->default('no');
            $table->uuid('email_approval_payment_template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
