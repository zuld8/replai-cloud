<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('package_transactions', function (Blueprint $t) {
            if (!Schema::hasColumn('package_transactions', 'message_limit_option'))
                $t->string('message_limit_option', 3)->default('no')->after('message_limit');
            if (!Schema::hasColumn('package_transactions', 'message_limit_priode'))
                $t->string('message_limit_priode', 10)->nullable()->after('message_limit_option');
        });
    }
    public function down(): void {
        Schema::table('package_transactions', function (Blueprint $t) {
            $t->dropColumnIfExists('message_limit_option');
            $t->dropColumnIfExists('message_limit_priode');
        });
    }
};
