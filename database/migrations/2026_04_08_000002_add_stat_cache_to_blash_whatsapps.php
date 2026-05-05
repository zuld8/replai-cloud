<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->unsignedInteger('stat_total')->default(0)->after('status');
            $table->unsignedInteger('stat_sent')->default(0)->after('stat_total');
            $table->unsignedInteger('stat_failed')->default(0)->after('stat_sent');
            $table->unsignedInteger('stat_delivered')->default(0)->after('stat_failed');
            $table->unsignedInteger('stat_read')->default(0)->after('stat_delivered');
            $table->unsignedInteger('stat_delivery_failed')->default(0)->after('stat_read');
            $table->timestamp('stat_updated_at')->nullable()->after('stat_delivery_failed');
        });
    }

    public function down(): void
    {
        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->dropColumn(['stat_total', 'stat_sent', 'stat_failed', 
                'stat_delivered', 'stat_read', 'stat_delivery_failed', 'stat_updated_at']);
        });
    }
};
