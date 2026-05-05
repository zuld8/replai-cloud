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
        Schema::create('package_transaction_payments', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('package_transaction_id')->index();
            $table->string('method')->nullable();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('to_bank')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_transaction_payments');
    }
};
