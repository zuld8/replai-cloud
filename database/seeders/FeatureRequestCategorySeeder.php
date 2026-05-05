<?php

namespace Database\Seeders;

use App\Models\FeatureRequestCategory;
use Illuminate\Database\Seeder;

class FeatureRequestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'UI/UX',
            'Integrasi WhatsApp',
            'Laporan dan Analitik',
            'Manajemen Kontak',
            'Otomatisasi',
            'Keamanan',
            'Performa Aplikasi',
            'Lainnya'
        ];

        foreach ($categories as $category) {
            FeatureRequestCategory::create([
                'name' => $category
            ]);
        }
    }
}
