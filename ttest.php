<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\ChatBot\HistoryChatDetail;
try {
    $r = HistoryChatDetail::where('total_tokens', '>', 0)->sum('total_tokens');
    echo 'OK today sum: ' . $r . PHP_EOL;
} catch(Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
