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
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('ai_response', 22, 4)->default(0)->after('chatbot_limit');
            $table->enum('livechat_limit',['yes','no'])->default('no')->after('ai_response');
            $table->decimal('limit_livechat',22,4)->default(1)->after('livechat_limit');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->decimal('ai_response', 22, 4)->default(0)->after('chatbot_limit');
            $table->enum('livechat_limit',['yes','no'])->default('no')->after('ai_response');
            $table->decimal('limit_livechat',22,4)->default(1)->after('livechat_limit');

            $table->enum('type',['package','topup'])->default('package')->after('limit_livechat');
            $table->uuid('package_id')->nullable()->change();
            $table->string('expire_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
