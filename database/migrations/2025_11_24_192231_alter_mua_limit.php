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
            $table->integer('mua_limit')->default(0)->after('ai_response');
            $table->string('mua_limit_optin')->default('no')->after('mua_limit');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
             $table->string('type')->default('package')->change();
            $table->integer('mua_limit')->default(0)->after('ai_response');
            $table->string('mua_limit_optin')->default('no')->after('mua_limit');

            $table->integer('new_order_mua_limit')->default(0)->after('new_order_ai_response');
            $table->integer('using_mua_limit')->default(0)->after('new_order_mua_limit');
        });

        Schema::table('internal_settings', function (Blueprint $table) {
            $table->integer('mua_per_price')->default(0)->after('token_per_price');
            $table->decimal('price_mua',22,4)->default(0)->after('mua_per_price');
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
