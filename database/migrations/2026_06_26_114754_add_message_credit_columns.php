<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add message_limit columns to packages table
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'message_limit_option')) {
                $table->string('message_limit_option', 3)->default('no')->after('ai_response');
            }
            if (!Schema::hasColumn('packages', 'message_limit')) {
                $table->integer('message_limit')->default(0)->after('message_limit_option');
            }
            if (!Schema::hasColumn('packages', 'message_limit_priode')) {
                $table->string('message_limit_priode', 10)->nullable()->after('message_limit');
            }
        });

        // Add message_limit columns to package_transactions table
        Schema::table('package_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('package_transactions', 'message_limit')) {
                $table->integer('message_limit')->default(0)->after('ai_response');
            }
            if (!Schema::hasColumn('package_transactions', 'using_message_limit')) {
                $table->integer('using_message_limit')->default(0)->after('message_limit');
            }
            if (!Schema::hasColumn('package_transactions', 'new_order_message_limit')) {
                $table->integer('new_order_message_limit')->default(0)->nullable()->after('using_message_limit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumnIfExists('message_limit_option');
            $table->dropColumnIfExists('message_limit');
            $table->dropColumnIfExists('message_limit_priode');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->dropColumnIfExists('message_limit');
            $table->dropColumnIfExists('using_message_limit');
            $table->dropColumnIfExists('new_order_message_limit');
        });
    }
};
