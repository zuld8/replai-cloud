<?php
// Direct WABA Blast Script
// Usage: php direct_blast.php > /tmp/blast_output.log 2>&1

use App\Models\Blash\BlashWhatsapp;
use App\Models\Blash\BlashDetail;
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
$tplObs = new WhatsappTemplateServiceObserver();

$total_sent = 0;
$total_fail = 0;

foreach ($BLAST_IDS as $blastId) {
    $blast = BlashWhatsapp::withoutGlobalScopes()->with('template')->find($blastId);
    if (!$blast) { echo "Blast $blastId not found\n"; flush(); continue; }
    
    // Get sample detail to find device_id
    $sampleDetail = DB::table('blash_details')
        ->where('blash_whatsapp_id', $blastId)
        ->where('status', 'no')
        ->first(['id', 'device_id', 'store_id']);
    
    if (!$sampleDetail) { echo "No pending details for {$blast->name}\n"; flush(); continue; }
    
    $wka = WhatsappKeyAccount::withoutGlobalScopes()
        ->where('id', $sampleDetail->device_id)
        ->where('status', 'active')
        ->first();
    
    if (!$wka) { echo "WKA not found for {$blast->name}\n"; flush(); continue; }
    
    $meta = MetaAccount::withoutGlobalScopes()->withoutTrashed()
        ->where('id', $wka->meta_account_id)->first();
    if (!$meta) { echo "Meta not found for {$blast->name}\n"; flush(); continue; }
    
    $config = $wka->meta_data ? json_decode($wka->meta_data, true) : [];
    $msgVar = [
        'appid' => $meta->app_id,
        'phoneid' => $config['whatsapp']['phone_number_id'] ?? null,
        'wabaid' => $meta->business_app,
        'access_token' => $meta->access_token,
    ];
    
    $templateContent = $tplObs->buildTemplate($blast);
    
    echo "\n=== {$blast->name} | WKA:{$wka->phone} | phoneid:{$msgVar['phoneid']} ===\n";
    flush();
    
    $details = DB::table('blash_details')
        ->where('blash_whatsapp_id', $blastId)
        ->where('status', 'no')
        ->get(['id', 'store_id']);
    
    echo "Total pending: " . count($details) . "\n"; flush();
    
    $sent = 0; $fail = 0;
    foreach ($details as $det) {
        $store = App\Models\Store\Store::withoutGlobalScopes()->find($det->store_id);
        if (!$store) {
            DB::table('blash_details')->where('id', $det->id)->update(['status'=>'yes','sending_status'=>'no','reports'=>'Store not found']);
            $fail++; continue;
        }
        
        try {
            $result = $wabaObs->sendTemplateMessage($store, $templateContent, $msgVar);
            $ok = ($result['status'] ?? 500) == 200;
            DB::table('blash_details')->where('id', $det->id)->update([
                'status' => 'yes',
                'sending_status' => $ok ? 'yes' : 'no',
                'delivery_status' => $ok ? 'sent' : 'failed',
                'wamid' => $ok ? ($result['messageid'] ?? null) : null,
                'sending' => now()->format('Y-m-d H:i:s'),
                'reports' => $ok ? null : ($result['message'] ?? 'API Error'),
            ]);
            if ($ok) { $sent++; } else { $fail++; }
        } catch (Exception $e) {
            DB::table('blash_details')->where('id', $det->id)->update([
                'status'=>'yes','sending_status'=>'no','reports'=>substr($e->getMessage(),0,200)
            ]);
            $fail++;
        }
        usleep(200000);
        if (($sent+$fail) % 50 == 0) { echo "  Progress: sent=$sent fail=$fail\n"; flush(); }
    }
    
    DB::table('blash_whatsapps')->where('id', $blastId)->update(['status'=>'success']);
    echo "DONE {$blast->name}: sent=$sent fail=$fail\n"; flush();
    $total_sent += $sent; $total_fail += $fail;
}
echo "\nTOTAL SENT=$total_sent FAIL=$total_fail\n";
