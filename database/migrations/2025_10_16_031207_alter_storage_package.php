<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('storage')->default(1)->after('telegram');
            $table->enum('type', ['storage', 'package'])->default('package')->after('name');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->integer('storage')->default(1)->after('telegram');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->string('type')->default('package')->change();
        });

        // DB::statement("ALTER TABLE package_transactions MODIFY COLUMN `type` ENUM('package','topup','storage') default 'package'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
