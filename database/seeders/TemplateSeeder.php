<?php

namespace Database\Seeders;

use App\Models\Cms\Template; 
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $data = [
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'WhatsCrmhub Theme',
                'code'          => 'template2',
                'thumbnail'     => 'uploads/templates/template02.png',
            ],
            [
                'id'            => Uuid::uuid4()->toString(),
                'name'          => 'Base Theme',
                'code'          => 'template1',
                'thumbnail'     => 'uploads/templates/template01.png',
            ]
        ];

        Template::insert($data);
    }
}
