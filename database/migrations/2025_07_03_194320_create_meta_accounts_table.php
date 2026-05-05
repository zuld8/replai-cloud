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
        Schema::create('meta_accounts', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('business_id')->nullable()->index();
            $table->uuid('merchant_id')->nullable()->index();
            $table->string('app_id')->nullable();
            $table->string('app_secret')->nullable();
            $table->string('business_app')->nullable();
            $table->text('access_token')->nullable();
            $table->string('name')->nullable();
            $table->longText('details')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_accounts');
    }
};
