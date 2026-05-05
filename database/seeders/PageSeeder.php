<?php

namespace Database\Seeders;

use App\Models\Cms\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'            => Uuid::uuid4()->toString(),
                'page'          => 'home',
                'name'          => 'Home Page',
                'content'       => '-'
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'page'          => 'contact',
                'name'          => 'Contact Page',
                'content'       => '-'
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'page'          => 'pricing',
                'name'          => 'Pricing Page',
                'content'       => '-'
            ],
        ];

        Page::insert($data);
    }
}
