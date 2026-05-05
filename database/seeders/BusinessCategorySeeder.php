<?php

namespace Database\Seeders;

use App\Models\Merchant\MerchantCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'Business Retail',
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'Sofware Development',
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'Saas Business',
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'Service Business',
            ],
        ];

        MerchantCategory::insert($data);
    }
}
