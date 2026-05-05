<?php

namespace Database\Seeders;

use App\Models\InternalSetting;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class InternalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'mailer'            => 'SMTP',
            'local_api_key'     => Uuid::uuid4()->toString(),
            'mail_encryption'   => 'tls',
            'use_whatsapp'      => 'internal',
            'default_lang'      => 'en',
            'ai_option'         => 'chatgpt',
            'name'              => 'WhatsCrmHub.ai'
        ]);

        InternalSetting::create([
            'app_name'              => 'WhatsMail - Solution For Marketing and Saas Platform',
            'logo'                  => 'assets/img/color-logo.png',
            'icon'                  => 'assets/img/icon.png',
            'loader'                => 'assets/img/loader.png',
            'white_logo'            => 'assets/img/white-logo.png',
            'meta_keyword'          => 'whatsapp sender, whatsapp api',
            'frontend'              => 'no',
            'blog'                  => 'no',
            'pricing'               => 'no',
            'contact'               => 'no',
        ]);
    }
}
