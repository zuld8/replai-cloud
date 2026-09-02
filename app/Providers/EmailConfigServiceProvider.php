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
     * Override konfigurasi mail dari database (settings global, merchant_id = NULL).
     * Key yang dipakai = Laravel 11 (mail.mailers.smtp.*), BUKAN Laravel 5 (mail.host).
     */
    public function boot(): void
    {
        $installed = Storage::disk('storage')->exists('installed');

        if ($installed) {
            if (Schema::hasTable('settings')) {
                $s = Setting::withoutGlobalScopes()->where("merchant_id", null)->first();

                if ($s && !empty($s->mail_host)) {
                    // Laravel 11: WAJIB pakai key ini. 'mail.host' / 'mail.driver' = warisan Laravel 5, tidak berpengaruh.
                    Config::set('mail.default', 'smtp');
                    Config::set('mail.mailers.smtp.transport',  'smtp');
                    Config::set('mail.mailers.smtp.host',       $s->mail_host);
                    Config::set('mail.mailers.smtp.port',       (int) $s->mail_port);
                    Config::set('mail.mailers.smtp.username',   $s->mail_username);
                    Config::set('mail.mailers.smtp.password',   $s->mail_password);
                    Config::set('mail.mailers.smtp.encryption', $s->mail_encryption ?: 'tls');
                    Config::set('mail.from.address',            $s->mail_from_address ?: 'noreply@replai.id');
                    Config::set('mail.from.name',               $s->mail_from_name ?: 'Replai.id');
                }
            }
        }
    }
}

