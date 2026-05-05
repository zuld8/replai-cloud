<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

$BLAST_IDS = [
    '24f62300-01c2-4a3b-8b27-ba466eef029e',
    'c7da6e87-6547-40ab-aa88-5fc6c755ddae',
    '4d633da9-22dd-463c-97e9-6df7ea612ed4',
    'e327bf0c-2771-418e-a68a-885e301795d4',
];

$total_added = 0;

foreach ($BLAST_IDS as $blastId) {
    $blast = DB::table('blash_whatsapps')->where('id', $blastId)->first();
    if (!$blast) continue;
    
    // Get body text from metadata
    $metadata = $blast->metadata ? json_decode($blast->metadata, true) : [];
    $bodyText = $metadata['body']['text'] ?? "Broadcast: {$blast->name}";
    
    // Get WKA for this blast
    $sampleDetail = DB::table('blash_details')
        ->where('blash_whatsapp_id', $blastId)
        ->whereNotNull('wamid')
        ->first(['device_id', 'wamid', 'sending']);
    
    if (!$sampleDetail) {
        echo "No sent details for {$blast->name}\n"; continue;
    }
    $wkaId = $sampleDetail->device_id;
    $sendTime = $sampleDetail->sending ?? now()->format('Y-m-d H:i:s');
    
    echo "\n=== {$blast->name} | WKA: $wkaId ===\n"; flush();
    
    // Get all sent details (wamid IS NOT NULL)
    $sentDetails = DB::table('blash_details as bd')
        ->join('stores as s', 's.id', 'bd.store_id')
        ->where('bd.blash_whatsapp_id', $blastId)
        ->whereNotNull('bd.wamid')
        ->get(['bd.phone', 'bd.wamid', 'bd.sending', 's.name as store_name', 's.id as store_id']);
    
    echo "Sent details: " . count($sentDetails) . "\n"; flush();
    
    $added = 0; $skip = 0;
    foreach ($sentDetails as $det) {
        // Find existing HistoryChat for this phone + WKA
        $history = DB::table('history_chats')
            ->where('whatsapp_waba_id', $wkaId)
            ->where('from_number', $det->phone)
            ->first(['id']);
        
        if (!$history) {
            // Create new HistoryChat if not exists
            $hcId = Uuid::uuid4()->toString();
            DB::table('history_chats')->insert([
                'id' => $hcId,
                'whatsapp_waba_id' => $wkaId,
                'name' => $det->store_name ?? '-',
                'merchant_id' => $blast->merchant_id,
                'type' => 'personal',
                'from_number' => $det->phone,
                'business_id' => $blast->business_id,
                'from' => 'waba',
                'takeover' => 'no',
                'status' => 'open',
                'created_at' => $det->sending ?? now(),
                'updated_at' => now(),
                'last_message_at' => $det->sending ?? now(),
            ]);
            $history = (object)['id' => $hcId];
        }
        
        // Check if message detail already exists (by wamid/messageid)
        $existingDetail = DB::table('history_chat_details')
            ->where('history_chat_id', $history->id)
            ->where('messageid', $det->wamid)
            ->first(['id']);
        
        if (!$existingDetail) {
            // Add message detail entry
            DB::table('history_chat_details')->insert([
                'id' => Uuid::uuid4()->toString(),
                'history_chat_id' => $history->id,
                'from' => 'device',
                'message' => $bodyText,
                'type' => 'text',
                'is_read' => 'yes',
                'messageid' => $det->wamid,
                'created_at' => $det->sending ?? now(),
                'updated_at' => now(),
            ]);
            
            // Update history_chats last_message_at
            DB::table('history_chats')->where('id', $history->id)->update([
                'last_message_at' => $det->sending ?? now(),
                'updated_at' => now(),
                'status' => 'open',
            ]);
            
            $added++; $total_added++;
        } else {
            $skip++;
        }
        
        if (($added+$skip) % 100 == 0) {
            echo "  Progress: added=$added skip=$skip\n"; flush();
        }
    }
    echo "DONE {$blast->name}: added=$added skip=$skip\n"; flush();
}

echo "\n=== TOTAL messages added to CRM: $total_added ===\n";
