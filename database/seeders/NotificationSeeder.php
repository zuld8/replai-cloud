<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationSetting::create([
            'whatsapp_register_template'                => '7515f6b9-8bd8-4025-9645-9443f9a80f7c',
            'whatsapp_buy_package_template'             => '53be29da-046d-4d87-a570-e6cc66f6b50a',
            'whatsapp_package_payment_template'         => '510b32e9-d6d9-4c75-83ae-f95cdfcc96e3',
            'whatsapp_package_user_template'            => '6e84e048-e713-4cd0-95c8-d76afb2fdbb4',
            'whatsapp_approval_payment_template'        => 'e523f58d-cbfd-4fda-831c-d4eec49e8ce2',

            'email_register_template'                   => '2f950763-a45d-4628-88f4-244901371f4e', 
            'email_buy_package_template'                => '0ed14b39-b4e3-4618-84e0-de7380c6572b', 
            'email_package_payment_template'            => 'b86d6357-3cc9-417e-aacc-7a0a903bec6a', 
            'email_package_user_template'               => '04399777-de03-4945-99f0-56494cfd7b9d', 
            'email_approval_payment_template'           => 'be0a1118-0271-44f7-a1f2-5a67d23de2db',
        ]);
    }
}
