<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = my_user();

        // Admin bypass
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // User biasa check permission
        if ($user && $user->can($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
