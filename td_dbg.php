
$bc = App\\Models\\Blash\\BlashWhatsapp::find('92307f77-e67e-4428-a2d0-1d3842920acf');
echo "BC: " . $bc->name . ", status: " . $bc->status . ", schedule: " . $bc->schedule . "\n";
echo "category_id: " . $bc->category_id . "\n";
echo "devices: " . $bc->devices . "\n";
echo "waba: " . $bc->waba . "\n";
echo "business_id: " . $bc->business_id . "\n";

use App\\Models\\Store\\Store;
$storeQuery = Store::withoutGlobalScopes()
    ->where('business_id', $bc->business_id)
    ->where(function ($q) use ($bc) {
        return $bc->category_id != null ? $q->where("category_id", $bc->category_id) : '';
    })
    ->where("phone", "!=", null)
    ->where("status", "no");

echo "Stores count: " . $storeQuery->count() . "\n";

use App\\Models\\WhatsappKeyAccount;
$device = WhatsappKeyAccount::where('id', $bc->devices)->first();
echo "Device: " . ($device ? $device->phone . " status=" . $device->status : "NOT FOUND") . "\n";
