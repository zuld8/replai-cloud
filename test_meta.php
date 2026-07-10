
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$meta = DB::table('meta_accounts')->where('id', 'ee1186ab-6c62-4a31-865e-81c61cc1e41d')->first();
$waba = DB::table('whatsapp_devices')->where('meta_account_id', 'ee1186ab-6c62-4a31-865e-81c61cc1e41d')->first();
echo "META:\n";
print_r($meta);
echo "\nWABA DEVICE:\n";
print_r($waba);

// check recent history for test numbers sent by user
$recent = DB::table('history_chats')->orderBy('created_at', 'desc')->limit(5)->get(['from_number']);
echo "\nRECENT CHATS:\n";
print_r($recent);

