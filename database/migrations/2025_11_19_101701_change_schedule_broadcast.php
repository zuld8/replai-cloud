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

        // DB::statement("
        //     UPDATE blash_whatsapps
        //     SET schedule = STR_TO_DATE(schedule, '%Y-%m-%dT%H:%i')
        //     WHERE schedule LIKE '%T%';
        // ");


        // Cek database driver
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL - pakai raw SQL dengan USING clause
            DB::statement('ALTER TABLE blash_whatsapps ALTER COLUMN schedule TYPE timestamp(0) USING schedule::timestamp');
            DB::statement('ALTER TABLE blash_whatsapps ALTER COLUMN schedule DROP NOT NULL');

            DB::statement('ALTER TABLE blash_details ALTER COLUMN schedule TYPE timestamp(0) USING schedule::timestamp');
            DB::statement('ALTER TABLE blash_details ALTER COLUMN schedule DROP NOT NULL');

            DB::statement('ALTER TABLE scrappings ALTER COLUMN schedule TYPE timestamp(0) USING schedule::timestamp');
            DB::statement('ALTER TABLE scrappings ALTER COLUMN schedule DROP NOT NULL');
        } else {
            // MySQL - pakai Laravel Schema biasa
            Schema::table('blash_whatsapps', function (Blueprint $table) {
                $table->dateTime('schedule')->nullable()->change();
            });

            Schema::table('blash_details', function (Blueprint $table) {
                $table->dateTime('schedule')->nullable()->change();
            });

            Schema::table('scrappings', function (Blueprint $table) {
                $table->dateTime('schedule')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
