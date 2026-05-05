<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public static function getStats($userId)
    {
        return Cache::remember("dashboard_stats_{$userId}", 300, function() use ($userId) {
            return [
                'merchants_count' => DB::table('merchants')->where('user_id', $userId)->count(),
                'businesses_count' => DB::table('businesses')->where('user_id', $userId)->count(),
            ];
        });
    }
    
    public static function clearCache($userId)
    {
        Cache::forget("dashboard_stats_{$userId}");
    }
}
