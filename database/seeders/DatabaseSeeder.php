<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(InternalSettingSeeder::class);
        $this->call(BusinessCategorySeeder::class);
        $this->call(PageSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(WhatsappTemplateSeeder::class);
        $this->call(MailTemplateSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call(ModulFiturePermissionSeeder::class);
        // $this->call(TicketCategorySeeder::class);
        // $this->call(TicketSeeder::class);
    }
}
