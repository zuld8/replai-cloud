
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$template = DB::table('message_templates')->where('id', '850f3500-6528-4934-96a5-a2fdb99a2d69')->first();
$waba = DB::table('whatsapp_key_accounts')->where('id', 'ee1186ab-6c62-4a31-865e-81c61cc1e41d')->first();
echo "TEMPLATE:\n";
print_r($template);
echo "\nWABA:\n";
print_r($waba);
