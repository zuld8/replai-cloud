<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('package_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('package_transactions', 'expiry_reminder_sent')) {
                $table->json('expiry_reminder_sent')->nullable()->after('status')
                      ->comment('{"h7":true,"h3":true,"h1":true,"expired":true}');
            }
        });
    }
    public function down(): void {
        Schema::table('package_transactions', function (Blueprint $table) {
            $table->dropColumnIfExists('expiry_reminder_sent');
        });
    }
};
