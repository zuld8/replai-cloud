<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket\Ticket_category;
use Illuminate\Support\Str;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Support',
                'slug' => 'general-support',
                'description' => 'General customer support and inquiries',
                'is_active' => true,
            ],
            [
                'name' => 'Technical Issue',
                'slug' => 'technical-issue',
                'description' => 'Technical problems and bug reports',
                'is_active' => true,
            ],
            [
                'name' => 'Billing & Payment',
                'slug' => 'billing-payment',
                'description' => 'Billing inquiries and payment issues',
                'is_active' => true,
            ],
            [
                'name' => 'Sales Inquiry',
                'slug' => 'sales-inquiry',
                'description' => 'Sales questions and product inquiries',
                'is_active' => true,
            ],
            [
                'name' => 'Account Management',
                'slug' => 'account-management',
                'description' => 'Account settings and profile management',
                'is_active' => true,
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'New feature suggestions and requests',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ticket_category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
