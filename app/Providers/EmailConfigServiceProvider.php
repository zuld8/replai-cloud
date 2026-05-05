<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config; 
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EmailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $installed = Storage::disk('storage')->exists('installed');

        if ($installed) {
            if (Schema::hasTable('settings')) {
                $emailSettings  = Setting::withoutGlobalScopes()->where("merchant_id", null)->first(); 

                if ($emailSettings) {
                    Config::set('mail.driver', 'smtp');
                    Config::set('mail.host', $emailSettings->mail_host);
                    Config::set('mail.port', $emailSettings->mail_port);
                    Config::set('mail.username', $emailSettings->mail_username);
                    Config::set('mail.password', $emailSettings->mail_password);
                    Config::set('mail.encryption', $emailSettings->mail_encryption);
                    Config::set('mail.from.address', $emailSettings->mail_from_address);
                    Config::set('mail.from.name', $emailSettings->mail_from_name);
                }  
            }
        }
    }
}
