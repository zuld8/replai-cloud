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
        Schema::create('history_chats', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('device_id')->index();
            $table->uuid('merchant_id')->index()->nullable();
            $table->enum('type',['personal','group'])->default('personal');
            $table->string('from_number'); 
            $table->date('expire_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_chats');
    }
};
