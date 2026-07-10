
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$meta = DB::table('meta_accounts')->where('id', 'ee1186ab-6c62-4a31-865e-81c61cc1e41d')->first();
$device = DB::table('whatsapp_key_accounts')->where('meta_account_id', 'ee1186ab-6c62-4a31-865e-81c61cc1e41d')->first();
$template = DB::table('message_templates')->where('id', '850f3500-6528-4934-96a5-a2fdb99a2d69')->first();

$config = json_decode($device->meta_data, true) ?? [];
$phoneId = $config['whatsapp']['phone_number_id'] ?? null;
$accessToken = $meta->access_token;
$templateName = strtolower(preg_replace("/[^0-9a-zA-Z]/", "_", $template->name));

$payload = [
    'messaging_product' => 'whatsapp',
    'recipient_type'    => 'individual',
    'to'                => '6285187290654',
    'type'              => 'template',
    'template'          => [
        'name'       => $templateName,
        'language'   => ['code' => $template->lang ?? 'id'],
        'components' => [
            [
                'type' => 'BODY',
                'parameters' => [
                    ['type' => 'text', 'text' => 'Kelas Online'],
                    ['type' => 'text', 'text' => 'Bpk/Ibu'],
                    ['type' => 'text', 'text' => 'Replai'],
                    ['type' => 'text', 'text' => 'contoh@email.com'],
                    ['type' => 'text', 'text' => '39K'],
                    ['type' => 'text', 'text' => 'Tidak Ada']
                ]
            ]
        ]
    ],
];

$apiVersion = config('custom.api_waba_version', 'v18.0');
$url = "https://graph.facebook.com/{$apiVersion}/{$phoneId}/messages";
$response = Illuminate\Support\Facades\Http::withToken($accessToken)->post($url, $payload);
echo "RESPONSE:\n";
echo $response->body();
