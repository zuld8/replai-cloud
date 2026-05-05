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
        Schema::create('internal_settings', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('app_name');
            $table->string('logo')->default('uploads/setting/logo.png');
            $table->string('white_logo')->default('uploads/setting/white-logo.png');
            $table->string('icon')->default('uploads/settings/icon.png');
            $table->decimal('tax', 22, 4)->default(0);
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->enum('register', ['yes', 'no'])->default('yes');
            $table->enum('frontend', ['yes', 'no'])->default('yes');
            $table->enum('blog', ['yes', 'no'])->default('yes');
            $table->enum('pricing', ['yes', 'no'])->default('yes');
            $table->enum('contact', ['yes', 'no'])->default('yes');
            $table->string('copyright')->nullable();
            $table->text('footer_description')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('phone_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_settings');
    }
};
