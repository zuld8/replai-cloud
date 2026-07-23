<?php
require '/var/www/html/chat.replai.id/vendor/autoload.php';
$app = require '/var/www/html/chat.replai.id/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\ChatBot\FineTunnel;

set_time_limit(600);
DB::statement('SET SESSION max_execution_time=0');

$monthYear  = now()->format('Y-m');
$monthStart = now()->startOfMonth()->toDateTimeString();
$monthEnd   = now()->endOfMonth()->toDateTimeString();

echo "Admin warm START\n";

// 1. AI usage
echo "  1. ai_usage... ";
$row = DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
    ->whereBetween('created_at', [$monthStart, $monthEnd])
    ->where('from', 'device')
    ->selectRaw("SUM(CASE WHEN source='bot' THEN 1 ELSE 0 END) as ai_replies,
                 SUM(CASE WHEN source IN ('bot','flow') THEN 1 ELSE 0 END) as automated,
                 COUNT(*) as total_out")->first();
$total = (int)($row->total_out ?? 0);
$auto  = (int)($row->automated ?? 0);
Cache::put("admin_ai_usage_{$monthYear}", [
    'ai_replies' => (int)($row->ai_replies ?? 0),
    'automation' => $total > 0 ? round($auto / $total * 100) : 0,
    'training'   => FineTunnel::withoutGlobalScopes()->count(),
], 1800);
echo "OK\n";

// 2. Credit total
echo "  2. credit_total... ";
$credit = DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
    ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('credit_using');
Cache::put("admin_credit_ai_total_{$monthYear}", $credit, 1800);
echo "OK\n";

// 3. AI top
echo "  3. ai_top... ";
$aiTop = HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
    ->whereBetween('history_chat_details.created_at', [$monthStart, $monthEnd])
    ->where('history_chat_details.source', 'bot')
    ->selectRaw('history_chats.business_id, COUNT(*) as n')
    ->groupBy('history_chats.business_id')
    ->orderByDesc('n')->limit(5)->get();
Cache::put("admin_ai_top_{$monthYear}", $aiTop, 1800);
echo "OK\n";

// 4. Active biz (berat — 7d JOIN+GROUP)
echo "  4. active_biz (7d, mungkin 2-3 mnt)... ";
$activeBiz = HistoryChatDetail::join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
    ->where('history_chat_details.created_at', '>=', now()->subDays(7))
    ->selectRaw('history_chats.business_id,
                 MAX(history_chat_details.created_at) as last_activity,
                 COUNT(*) as chat_7d')
    ->groupBy('history_chats.business_id')
    ->orderByDesc('last_activity')->limit(10)->get();
Cache::put("admin_active_biz", $activeBiz, 1800);
echo "OK (" . $activeBiz->count() . " bisnis)\n";

// 5. Credit chart
echo "  5. credit_chart... ";
$creditChart = DB::table(DB::raw('`history_chat_details` FORCE INDEX (`idx_hcd_created_source`)'))
    ->selectRaw("DATE(created_at) as date, sum(credit_using) as count")
    ->whereBetween("created_at", [$monthStart, $monthEnd])
    ->groupBy("date")->orderBy("date")->get();
Cache::put("admin_credit_ai_{$monthYear}", $creditChart, 1800);
echo "OK (" . $creditChart->count() . " days)\n";

echo "DONE\n";
