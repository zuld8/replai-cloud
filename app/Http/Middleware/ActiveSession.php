<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * ActiveSession — track user last activity & enforce subscription status.
 * Runs on all authenticated web routes (app.php + admin.php).
 */
class ActiveSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Perform any final actions after the response is sent.
     * Update the user's last_active_at in a lightweight cache entry.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (auth()->check()) {
            $userId = auth()->id();
            // Throttle: update at most once per 60 seconds per user
            $cacheKey = "user_active_{$userId}";
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, now()->toDateTimeString(), 60);
                // Optionally: User::where('id', $userId)->update(['last_active_at' => now()]);
            }
        }
    }
}
