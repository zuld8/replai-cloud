<?php

use App\Http\Middleware\CanInstall;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\Cors;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use App\Http\Middleware\LicenseCheck;
use App\Http\Middleware\PackageActivation;
use App\Http\Middleware\SetLocaleFromSession;
use App\Http\Middleware\StarterApp;
use App\Http\Middleware\WebAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware(['web', 'license_check'])
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'user', 'verified', 'active_session'])
                ->prefix('app')
                ->group(base_path('routes/app.php'));

            Route::middleware(['web', 'auth', 'admin', 'active_session'])
                ->prefix('administrator')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['api'])
                ->prefix('api-app')
                ->group(base_path('routes/api.php'));
        },
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['cache.dashboard' => \App\Http\Middleware\CacheDashboard::class]);
        
        $middleware->appendToGroup('web', SetLocaleFromSession::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CacheDashboard::class);
        $middleware->alias([
            'check_permission'  => CheckPermission::class,
            'install'           => CanInstall::class,
            'admin'             => IsAdmin::class,
            'user'              => IsUser::class,
            'package_active'    => PackageActivation::class,
            'web_access'        => WebAccess::class,
            'starter_app'       => StarterApp::class,
            'license_check'     => LicenseCheck::class,
            'cors'              => Cors::class
        ]);
    })->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
