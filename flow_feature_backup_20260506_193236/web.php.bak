<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Auth::routes([
    'verify'    => true,
    'register'  => true,
]);

$installed = Storage::disk('storage')->exists('installed');

Route::get('/authentication', function () {
    return redirect()->route('login');
});
// Route::get('posts/{post:slug}', function (Post $post) {
//     return view('posts.show', compact('post'));
// });

if ($installed == false) {

    Route::get('/login', function () {
        return redirect('/install');
    });

    Route::get('/', function () {
        return redirect('/install');
    });
} else {
    Route::get('/', [HomeController::class, 'redirect']);
    Route::get('set-lang/{lang}', [SettingsController::class, 'setLang'])->name('setlang');
    Route::post('register-user', [RegisterController::class, 'createUser'])->name('register.user');
    Route::get('policy', [HomeController::class, 'policy']);
    Route::get('term', [HomeController::class, 'term']);

    Route::get('/track-ticket', function () {
        return view('public.track-ticket');
    })->name('track.ticket');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('chats', [HomeController::class, 'chats']);

    // Affiliate Referral Tracking (public, no auth needed)
    Route::get('ref/{code}', [\App\Http\Controllers\Starter\AffiliateController::class, 'trackClick'])->name('affiliate.ref');

    Route::prefix('web')->middleware(['web_access'])->group(function () {

        Route::prefix('blogs')->group(function () {
            Route::get('/', [WebController::class, 'blogs'])->name('web.blogs');
            Route::get('/detail/{slug}', [WebController::class, 'blogDetail'])->name('web.blogs.detail');
        });

        Route::get('/', [WebController::class, 'index'])->name('web.home');
        Route::get('/pricing', [WebController::class, 'pricing'])->name('web.pricing');
        Route::get('/contact', [WebController::class, 'contact'])->name('web.contact');
        Route::get('/{page}', [WebController::class, 'other'])->name('web.page');
    });

    Route::prefix('provider')->group(function () {

        Route::prefix('facebook')->group(function () {
            Route::get('callback', [HomeController::class, 'handleFacebookCallback'])->name('login.facebook.callback');
            Route::get('redirect', [HomeController::class, 'redirectToFacebook'])->name('login.facebook');
        });
    
    });
}
