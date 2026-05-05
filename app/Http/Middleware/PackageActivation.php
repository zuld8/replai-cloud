<?php

namespace App\Http\Middleware;

use App\Models\Merchant\Merchant;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PackageActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (my_user()->role == 'user') {

                if(!my_business()) {
                    return redirect()->route('starter.business.index')->with(['gagal' => 'Silahkan Pilih Bisnis terlebih dahulu']);
                }

                if (my_business()) {
                    // FIX: Cache per merchant 30 detik
                    $merchantId  = my_user()->merchant_id;
                    $businessId  = my_business();
                    $cacheKey    = 'pkg_active_' . $merchantId . '_' . $businessId;

                    $isActive = Cache::remember($cacheKey, 30, function () use ($merchantId, $businessId) {
                        $merchant = Merchant::where('id', $merchantId)->first(['id', 'status']);
                        if (!$merchant) return 'no_merchant';
                        if ($merchant->status == 'no') return 'merchant_inactive';
                        $business = Setting::where('merchant_id', $merchant->id)
                                           ->where('id', $businessId)
                                           ->first(['id']); // FIX: hanya select id, package_active adalah accessor
                        if ($business && $business->package_active) return 'active';
                        return 'inactive';
                    });

                    if ($isActive === 'no_merchant') {
                        return redirect()->route('starter.business.index')->with(['gagal' => 'Silahkan Pilih Bisnis terlebih dahulu']);
                    }
                    if ($isActive === 'merchant_inactive') {
                        return redirect()->route('starter.business.index')->with(['gagal' => __('starter.business_not_active')]);
                    }
                    if ($isActive === 'active') {
                        return $next($request);
                    }
                }

                return redirect()->route('starter.business.index')->with(['gagal' => __('starter.package_plan_desc')]);
            }
        }

        return $next($request);
    }
}
