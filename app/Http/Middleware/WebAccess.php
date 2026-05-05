<?php

namespace App\Http\Middleware;

use App\Models\InternalSetting;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // FIX: Cache 5 menit
        $settings = Cache::remember('internal_settings_frontend', 300, function () {
            return InternalSetting::first(['frontend']);
        });

        if ($settings->frontend == 'no') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
