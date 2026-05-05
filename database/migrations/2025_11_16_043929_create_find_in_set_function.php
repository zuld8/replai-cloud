<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared("
                CREATE OR REPLACE FUNCTION find_in_set(needle TEXT, haystack TEXT)
                RETURNS BOOLEAN AS $$
                BEGIN
                    IF haystack IS NULL OR needle IS NULL THEN
                        RETURN FALSE;
                    END IF;
                    
                    RETURN needle = ANY(string_to_array(haystack, ','));
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared("DROP FUNCTION IF EXISTS find_in_set(TEXT, TEXT);");
        }
    }
};
