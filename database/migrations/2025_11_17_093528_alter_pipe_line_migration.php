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
        Schema::table('labels', function (Blueprint $table) {
            if (!Schema::hasColumn('labels', 'is_closeable')) {
                $table->enum('is_closeable', ['yes', 'no'])->default('no')->after('type');
            }
        });

        // 2. Create default pipeline segments untuk business yang belum punya
        DB::transaction(function () {
            $businesses = DB::table('settings')->pluck('id');

            foreach ($businesses as $businessId) {
                // Cek apakah business sudah punya pipeline
                $hasSegment = DB::table('pipeline_segments')
                    ->where('business_id', $businessId)
                    ->exists();

                if (!$hasSegment) {
                    // Create default pipeline
                    $segmentId = DB::table('pipeline_segments')->insertGetId([
                        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                        'name' => 'Default Pipeline',
                        'color' => '#3b82f6',
                        'business_id' => $businessId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Create default labels
                    $defaultLabels = [ 
                        ['name' => 'Penawaran Terkirim', 'color' => '#fbbf24', 'position' => 2, 'is_closeable' => 'no'],
                        ['name' => 'Negosiasi', 'color' => '#f59e0b', 'position' => 3, 'is_closeable' => 'no'],
                        ['name' => 'Penjualan Berhasil', 'color' => '#10b981', 'position' => 4, 'is_closeable' => 'yes'],
                        ['name' => 'Penjualan Gagal', 'color' => '#6b7280', 'position' => 5, 'is_closeable' => 'yes'],
                    ];

                    foreach ($defaultLabels as $label) {
                        DB::table('labels')->insert([
                            'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                            'name' => $label['name'],
                            'color' => $label['color'],
                            'position' => $label['position'],
                            'type' => 'crm',
                            'is_closeable' => $label['is_closeable'],
                            'is_default' => 'no',
                            'pipeline_segment_id' => $segmentId,
                            'business_id' => $businessId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
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
