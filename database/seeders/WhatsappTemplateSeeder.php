<?php

namespace Database\Seeders;

use App\Models\Master\MessageTemplate; 
use Illuminate\Database\Seeder;

class WhatsappTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'                => '510b32e9-d6d9-4c75-83ae-f95cdfcc96e3',
                'name'              => 'Subscription Package Payment',
                'message'           => 'Hello Admin,

The payment for the following subscription package has been successfully processed.

Payment Details:
- Business Name : *{business_name}*
- User Name: *{name}* 
- Package Name: *{package_name}*
- Payment Amount: *{payment_amount}*
- From Bank : *{from_bank}*
- To Bank : *{to_bank}*
- Payment Date: *{date}*

Please check the admin dashboard for more information.

Best regards,
*{app_name}* Team',
                'type'              => 'whatsapp',
                'type_content'      => 'description',
                'button_or_list'    => null
            ],
            [
                'id'                => '53be29da-046d-4d87-a570-e6cc66f6b50a',
                'name'              => 'Subscription Package Purchase',
                'message'           => 'Hello Admin,

The following user has just purchased a subscription package on our platform.

Purchase Details:
- Business Name: *{business_name}*
- User Name : *{name}*
- Package Name: *{package_name}*
- Subtotal : *{subtotal}*
- Purchase Date: *{date}*

Please check the admin dashboard for further details.

Best regards,
*{app_name}* Team',
                'type'              => 'whatsapp',
                'type_content'      => 'description',
                'button_or_list'    => null
            ],
            [
                'id'                => '6e84e048-e713-4cd0-95c8-d76afb2fdbb4',
                'name'              => 'Payment Received - Awaiting Approval',
                'message'           => 'Hello *{name}*,

Thank you for your payment! We have received your payment for the subscription package.

Payment Details:
- Package Name: *{package_name}*
- Payment Amount: *{payment_amount}*
- Payment Date: *{date}*
- From Bank : *{from_bank}*

Please note that your payment is currently being reviewed by our team. Your subscription package will be activated once the payment is approved. We appreciate your patience.

You will receive another notification once your subscription is active.

Best regards,
*{app_name}* Team',
                'type'              => 'whatsapp',
                'type_content'      => 'description',
                'button_or_list'    => null
            ],
            [
                'id'                => '7515f6b9-8bd8-4025-9645-9443f9a80f7c',
                'name'              => 'User Registration',
                'message'           => 'Hello Admin,

We would like to inform you that a new user has successfully registered on our platform.

User Details:
- Business Name : *{business_name}*
- User Name : *{name}*
- Email: *{email}*
* Phone : *{phone}*
- Registration Date: *{date}*

Please check the admin dashboard for more details.

Best regards,
*{app_name}* Team',
                'type'              => 'whatsapp',
                'type_content'      => 'description',
                'button_or_list'    => null
            ],
            [
                'id'                => 'e523f58d-cbfd-4fda-831c-d4eec49e8ce2',
                'name'              => 'Payment Approved - Subscription Activated',
                'message'           => 'Hello [User Name],

Good news! Your payment has been approved, and your subscription package is now active.

Subscription Details:
- Package Name: *{package_name}*
- Activation Date: *{active_date}*
- Expiry Date: *{expire_date}*

You can now enjoy all the features and benefits of your subscription. If you have any questions or need further assistance, feel free to contact our support team.

Thank you for choosing *{app_name}*!

Best regards,
*{app_name}* Team',
                'type'              => 'whatsapp',
                'type_content'      => 'description',
                'button_or_list'    => null
            ],
        ];

        MessageTemplate::insert($data);
    }
}
