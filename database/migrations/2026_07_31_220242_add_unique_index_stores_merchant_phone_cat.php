<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Layer 3b: rem DB — tolak duplikat phone+category per merchant
            // MySQL: NULL dianggap distinct → kontak tanpa phone tidak terblokir
            $table->unique(['merchant_id', 'phone', 'category_id'], 'uq_stores_merchant_phone_cat');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropUnique('uq_stores_merchant_phone_cat');
        });
    }
};
