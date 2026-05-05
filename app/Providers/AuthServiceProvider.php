<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Define Gate untuk admin bypass
        Gate::before(function ($user, $ability) {
            // Jika role = 'admin', bypass semua permission check
            if ($user->role === 'admin') {
                return true;
            }

            // Business owner: role='user' tanpa role_id yang di-assign
            // Mereka adalah subscriber utama — dapat full akses ke fitur bisnis mereka
            if ($user->role === 'user' && empty($user->role_id)) {
                return true;
            }

            // Sub-user dengan role_id: ikuti permission dari role yang di-assign
            return null;
        });

        // Optional: Gate untuk check role 'admin' secara eksplisit
        Gate::define('is-admin', function ($user) {
            return $user->role === 'admin';
        });
    }
}
