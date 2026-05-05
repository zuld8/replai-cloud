<?php

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Blash\BlashWhatsapp;
use App\Models\WhatsappKeyAccount;
use App\Models\MetaAccount;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Observers\WhatsappOfficial\WhatsappTemplateServiceObserver;
use Illuminate\Support\Facades\DB;

$BLAST_IDS = [
    '24f62300-01c2-4a3b-8b27-ba466eef029e',
    'c7da6e87-6547-40ab-aa88-5fc6c755ddae',
    '4d633da9-22dd-463c-97e9-6df7ea612ed4',
    'e327bf0c-2771-418e-a68a-885e301795d4',
];

$wabaObs = new WhatsappOfficialServiceObserver();
$tplObs  = new WhatsappTemplateServiceObserver();
$total_sent = 0; $total_fail = 0;

foreach ($BLAST_IDS as $blastId) {
    $blast = BlashWhatsapp::withoutGlobalScopes()->with('template')->find($blastId);
    if (!$blast) { echo "Blast not found: $blastId\n"; continue; }
    
    $sampleDetail = DB::table('blash_details')
        ->where('blash_whatsapp_id', $blastId)
        ->where('status', 'no')->first(['id','device_id','store_id']);
    if (!$sampleDetail) { echo "No pending: {$blast->name}\n"; continue; }
    
    $wka = WhatsappKeyAccount::withoutGlobalScopes()
        ->where('id', $sampleDetail->device_id)->where('status','active')->first();
    if (!$wka) { echo "WKA not found\n"; continue; }
    
    $meta = MetaAccount::withoutGlobalScopes()->withoutTrashed()
        ->where('id', $wka->meta_account_id)->first();
    if (!$meta) { echo "Meta not found\n"; continue; }
    
    $config  = $wka->meta_data ? json_decode($wka->meta_data, true) : [];
    $msgVar  = ['appid' => $meta->app_id, 'phoneid' => $config['whatsapp']['phone_number_id'] ?? null, 'wabaid' => $meta->business_app, 'access_token' => $meta->access_token];
    $tplContent = $tplObs->buildTemplate($blast);
    
    echo "=== {$blast->name} | {$wka->phone} | phoneid:{$msgVar['phoneid']} ===\n"; flush();
    
    $details = DB::table('blash_details')->where('blash_whatsapp_id', $blastId)->where('status','no')->get(['id','store_id']);
    echo "Pending: " . count($details) . "\n"; flush();
    
    $sent = 0; $fail = 0;
    foreach ($details as $det) {
        $store = App\Models\Store\Store::withoutGlobalScopes()->find($det->store_id);
        if (!$store) { DB::table('blash_details')->where('id',$det->id)->update(['status'=>'yes','sending_status'=>'no','reports'=>'No store']); $fail++; continue; }
        try {
            $r = $wabaObs->sendTemplateMessage($store, $tplContent, $msgVar);
            $ok = ($r['status'] ?? 500) == 200;
            DB::table('blash_details')->where('id',$det->id)->update(['status'=>'yes','sending_status'=>$ok?'yes':'no','delivery_status'=>$ok?'sent':'failed','wamid'=>$ok?($r['messageid']??null):null,'sending'=>now()->format('Y-m-d H:i:s'),'reports'=>$ok?null:($r['message']??'API Error')]);
            if ($ok) $sent++; else $fail++;
        } catch (Exception $e) {
            DB::table('blash_details')->where('id',$det->id)->update(['status'=>'yes','sending_status'=>'no','reports'=>substr($e->getMessage(),0,200)]);
            $fail++;
        }
        usleep(200000);
        if (($sent+$fail) % 50 == 0) { echo "  sent=$sent fail=$fail\n"; flush(); }
    }
    
    DB::table('blash_whatsapps')->where('id',$blastId)->update(['status'=>'success']);
    echo "DONE {$blast->name}: sent=$sent fail=$fail\n"; flush();
    $total_sent += $sent; $total_fail += $fail;
}
echo "\n=== TOTAL sent=$total_sent fail=$total_fail ===\n";
