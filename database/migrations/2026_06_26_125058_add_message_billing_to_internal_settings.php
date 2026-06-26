<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internal_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('internal_settings', 'message_per_price')) {
                $table->integer('message_per_price')->default(100)->after('mua_per_price');
            }
            if (!Schema::hasColumn('internal_settings', 'price_message')) {
                $table->integer('price_message')->default(50000)->after('message_per_price');
            }
        });
    }
    public function down(): void {
        Schema::table('internal_settings', function (Blueprint $table) {
            $table->dropColumnIfExists('message_per_price');
            $table->dropColumnIfExists('price_message');
        });
    }
};
