<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Blash\BlashWhatsapp;
use App\Models\WhatsappKeyAccount;
use App\Models\MetaAccount;
use App\Models\ChatBot\HistoryChat;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Observers\WhatsappOfficial\WhatsappTemplateServiceObserver;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

$BLAST_IDS = [
    '24f62300-01c2-4a3b-8b27-ba466eef029e', // rosa3
    'c7da6e87-6547-40ab-aa88-5fc6c755ddae', // rosa2
    '4d633da9-22dd-463c-97e9-6df7ea612ed4', // rosa1
    'e327bf0c-2771-418e-a68a-885e301795d4', // intan
];

$wabaObs = new WhatsappOfficialServiceObserver();
$tplObs  = new WhatsappTemplateServiceObserver();
$total_sent = 0; $total_fail = 0; $total_history = 0;

function createHistoryChat($blastDetail, $wkaId, $blast) {
    global $total_history;
    try {
        $existing = DB::table('history_chats')
            ->where('whatsapp_waba_id', $wkaId)
            ->where('from_number', $blastDetail->phone)
            ->first(['id']);
        
        if (!$existing) {
            $hcId = Uuid::uuid4()->toString();
            DB::table('history_chats')->insert([
                'id' => $hcId,
                'whatsapp_waba_id' => $wkaId,
                'name' => $blastDetail->store_name ?? '-',
                'merchant_id' => $blast->merchant_id,
                'type' => 'personal',
                'from_number' => $blastDetail->phone,
                'business_id' => $blast->business_id,
                'from' => 'waba',
                'takeover' => 'no',
                'status' => 'open',
                'label' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Add message detail
            $bodyText = '';
            if ($blast->metadata) {
                $meta = json_decode($blast->metadata, true);
                $bodyText = $meta['body']['text'] ?? '';
            }
            
            DB::table('history_chat_details')->insert([
                'id' => Uuid::uuid4()->toString(),
                'history_chat_id' => $hcId,
                'file_path' => null,
                'file_type' => null,
                'file_size' => null,
                'type' => 'text',
                'is_read' => 'yes',
                'from' => 'device',
                'message' => $bodyText,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $total_history++;
        }
    } catch (Exception $e) {
        echo "  HistoryChat error: " . $e->getMessage() . "\n";
    }
}

foreach ($BLAST_IDS as $blastId) {
    $blast = BlashWhatsapp::withoutGlobalScopes()->with('template')->find($blastId);
    if (!$blast) { echo "Blast not found: $blastId\n"; continue; }
    
    // Get pending details (belum terkirim - status=no)
    $pendingDetails = DB::table('blash_details as bd')
        ->join('stores as s', 's.id', '=', 'bd.store_id')
        ->where('bd.blash_whatsapp_id', $blastId)
        ->where('bd.status', 'no')
        ->get(['bd.id', 'bd.store_id', 'bd.device_id', 'bd.phone', 's.name as store_name']);
    
    // ALSO: create HistoryChat for already-sent messages (wamid IS NOT NULL, history belum ada)
    $sentDetails = DB::table('blash_details as bd')
        ->join('stores as s', 's.id', '=', 'bd.store_id')
        ->where('bd.blash_whatsapp_id', $blastId)
        ->where('bd.status', 'yes')
        ->whereNotNull('bd.wamid')
        ->get(['bd.id', 'bd.store_id', 'bd.device_id', 'bd.phone', 's.name as store_name']);
    
    if (count($pendingDetails) == 0 && count($sentDetails) == 0) {
        echo "No pending/sent for {$blast->name}\n"; continue;
    }
    
    // Get WKA and Meta from sample (use first detail)
    $sampleDetail = count($pendingDetails) > 0 ? $pendingDetails[0] : $sentDetails[0];
    $wka = WhatsappKeyAccount::withoutGlobalScopes()
        ->where('id', $sampleDetail->device_id)->where('status','active')->first();
    if (!$wka) {
        $wka = WhatsappKeyAccount::withoutGlobalScopes()
            ->where('id', $sampleDetail->device_id)->first();
    }
    if (!$wka) { echo "WKA not found for {$blast->name}\n"; continue; }
    
    $meta = MetaAccount::withoutGlobalScopes()->withoutTrashed()
        ->where('id', $wka->meta_account_id)->first();
    if (!$meta) { echo "Meta not found\n"; continue; }
    
    $config = $wka->meta_data ? json_decode($wka->meta_data, true) : [];
    $msgVar = [
        'appid' => $meta->app_id,
        'phoneid' => $config['whatsapp']['phone_number_id'] ?? null,
        'wabaid' => $meta->business_app,
        'access_token' => $meta->access_token,
    ];
    $tplContent = $tplObs->buildTemplate($blast);
    
    echo "\n=== {$blast->name} | {$wka->phone} ===\n";
    echo "Pending: " . count($pendingDetails) . " | Already sent (need history): " . count($sentDetails) . "\n";
    flush();
    
    // 1. Create HistoryChat for already-sent messages that don't have history yet
    $history_created = 0;
    foreach ($sentDetails as $det) {
        createHistoryChat($det, $wka->id, $blast);
        $history_created++;
        if ($history_created % 100 == 0) { echo "  history: $history_created\n"; flush(); }
    }
    if ($history_created > 0) echo "  HistoryChat created for sent: $history_created\n";
    
    // 2. Send pending messages + create HistoryChat
    $sent = 0; $fail = 0;
    foreach ($pendingDetails as $det) {
        $store = App\Models\Store\Store::withoutGlobalScopes()->find($det->store_id);
        if (!$store) {
            DB::table('blash_details')->where('id',$det->id)->update(['status'=>'yes','sending_status'=>'no','reports'=>'No store']);
            $fail++; continue;
        }
        try {
            $r = $wabaObs->sendTemplateMessage($store, $tplContent, $msgVar);
            $ok = ($r['status'] ?? 500) == 200;
            $wamid = $ok ? ($r['messageid'] ?? null) : null;
            
            DB::table('blash_details')->where('id',$det->id)->update([
                'status' => 'yes',
                'sending_status' => $ok ? 'yes' : 'no',
                'delivery_status' => $ok ? 'sent' : 'failed',
                'wamid' => $wamid,
                'sending' => now()->format('Y-m-d H:i:s'),
                'reports' => $ok ? null : ($r['message'] ?? 'API Error'),
            ]);
            
            if ($ok) {
                $sent++;
                // Create HistoryChat for this sent message
                createHistoryChat($det, $wka->id, $blast);
            } else { $fail++; }
        } catch (Exception $e) {
            DB::table('blash_details')->where('id',$det->id)->update(['status'=>'yes','sending_status'=>'no','reports'=>substr($e->getMessage(),0,200)]);
            $fail++;
        }
        usleep(200000);
        if (($sent+$fail) % 100 == 0) { echo "  Progress: sent=$sent fail=$fail history=$total_history\n"; flush(); }
    }
    
    DB::table('blash_whatsapps')->where('id',$blastId)->update(['status'=>'success']);
    echo "DONE {$blast->name}: sent=$sent fail=$fail history=$total_history\n"; flush();
    $total_sent += $sent; $total_fail += $fail;
}
echo "\n=== TOTAL sent=$total_sent fail=$total_fail history_created=$total_history ===\n";
