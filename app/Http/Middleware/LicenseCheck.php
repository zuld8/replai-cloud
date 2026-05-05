<?php

namespace App\Http\Middleware;

use App\Models\Admin\License;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installed = Storage::disk('storage')->exists('installed');

        if ($installed == true) {
            // FIX: Cache result 5 menit - tidak perlu query DB setiap request
            $getLicense = Cache::remember('license_check', 300, function () {
                return License::first();
            });
            if ($getLicense == null) {
                return redirect('/license-key');
            }
        }

        return $next($request);
    }
}
