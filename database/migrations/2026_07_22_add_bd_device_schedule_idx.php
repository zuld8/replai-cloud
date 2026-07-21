<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Idempotent: cek dulu sebelum tambah
        if (!$this->hasIndex('blash_details', 'bd_device_schedule_idx')) {
            // INPLACE, LOCK=NONE → tidak block tulis di tabel 2.3jt baris
            \DB::statement('ALTER TABLE blash_details ADD INDEX bd_device_schedule_idx (device_id, schedule) ALGORITHM=INPLACE, LOCK=NONE');
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('blash_details', 'bd_device_schedule_idx')) {
            \DB::statement('ALTER TABLE blash_details DROP INDEX bd_device_schedule_idx');
        }
    }

    private function hasIndex(string $table, string $name): bool
    {
        $res = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$name]);
        return !empty($res);
    }
};
