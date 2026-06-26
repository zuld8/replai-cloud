<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditApiController extends Controller
{
    public function getCredit(Request $request)
    {
        try {
            $mysql  = DB::connection('mysql');
            $apiKey = $request->input('api_key');

            if (!$apiKey) {
                return response()->json(['status' => false, 'message' => 'api_key required'], 400);
            }

            $settings = $mysql->table('settings')
                ->where('local_api_key', $apiKey)
                ->select('id', 'name')
                ->first();

            if (!$settings) {
                return response()->json(['status' => false, 'message' => 'Api Key tidak valid'], 401);
            }

            $bid = $settings->id;

            $pkg = $mysql->table('package_transactions')
                ->where('business_id', $bid)
                ->where('type', 'package')
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->select('new_order_ai_response', 'using_credit_limit', 'expire_date')
                ->first();

            $mua = $mysql->table('package_transactions')
                ->where('business_id', $bid)
                ->where('type', 'mua')
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->select('new_order_ai_response', 'using_credit_limit')
                ->first();

            $pkgLimit  = $pkg ? (float)$pkg->new_order_ai_response : 0;
            $pkgUsed   = $pkg ? (float)$pkg->using_credit_limit : 0;
            $muaLimit  = $mua ? (float)$mua->new_order_ai_response : 0;
            $muaUsed   = $mua ? (float)$mua->using_credit_limit : 0;

            $totalLimit = $pkgLimit + $muaLimit;
            $totalUsed  = $pkgUsed + $muaUsed;
            $remaining  = max(0, $totalLimit - $totalUsed);
            $pct        = $totalLimit > 0 ? round($totalUsed / $totalLimit * 100, 2) : 0;

            // Message credit (Kredit Pesan)
            $msgPkg    = $mysql->table('package_transactions')
                ->where('business_id', $bid)->where('type', 'package')
                ->where('status', 'success')->orderBy('created_at', 'desc')
                ->first(['message_limit_option', 'message_limit', 'using_message_limit']);
            $msgTopup  = $mysql->table('package_transactions')
                ->where('business_id', $bid)->where('type', 'message_topup')
                ->where('status', 'success')->orderBy('created_at', 'desc')
                ->first(['message_limit', 'using_message_limit']);
            $msgOpt    = $msgPkg ? ($msgPkg->message_limit_option ?? 'no') : 'no';
            $msgLimit  = ($msgPkg ? (int)$msgPkg->message_limit : 0) + ($msgTopup ? (int)$msgTopup->message_limit : 0);
            $msgUsed   = ($msgPkg ? (int)$msgPkg->using_message_limit : 0) + ($msgTopup ? (int)$msgTopup->using_message_limit : 0);
            $msgSisa   = $msgOpt === 'no' ? -1 : max(0, $msgLimit - $msgUsed); // -1 = unlimited

            return response()->json([
                'status' => true,
                'data'   => [
                    'business'                => $settings->name,
                    'ai_credit_package'       => (int) max(0, $pkgLimit  - $pkgUsed),
                    'ai_credit_topup'         => (int) max(0, $muaLimit  - $muaUsed),
                    'ai_credit_total'         => (int) $remaining,
                    'ai_credit_used'          => (int) $totalUsed,
                    'ai_credit_limit'         => (int) $totalLimit,
                    'percentage_used'         => $pct,
                    'package_expire'          => $pkg->expire_date ?? null,
                    'message_credit_option'   => $msgOpt,
                    'message_credit_limit'    => $msgOpt === 'no' ? null : $msgLimit,
                    'message_credit_used'     => $msgUsed,
                    'message_credit_remaining'=> $msgOpt === 'no' ? null : $msgSisa,
                    'message_unlimited'       => $msgOpt !== 'yes',
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
                'line'   => $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }
}
