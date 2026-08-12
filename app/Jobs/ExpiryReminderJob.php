<?php

namespace App\Jobs;

use App\Mail\NotificationEmail;
use App\Models\Master\MessageTemplate;
use App\Models\NotificationSetting;
use App\Models\PackageTransaction;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class ExpiryReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;

    // Milestone key => daysFromExpiry (negatif = sudah lewat)
    const MILESTONES = [
        'h7'      => 7,
        'h3'      => 3,
        'h1'      => 1,
        'expired' => 0,  // expire_date = kemarin (H+1)
    ];

    public function handle(): void
    {
        $setting = NotificationSetting::first();
        if (!$setting) return;

        $today = Carbon::today();

        foreach (self::MILESTONES as $milestone => $days) {
            // Kalau expired: expire_date = kemarin
            // Kalau H-x: expire_date = today + x hari
            if ($milestone === 'expired') {
                $targetDate = $today->copy()->subDay()->toDateString(); // kemarin
            } else {
                $targetDate = $today->copy()->addDays($days)->toDateString();
            }

            // Ambil paket aktif yang expire_date cocok
            $transactions = PackageTransaction::with(['merchant.owner', 'package'])
                ->where('status', 'success')
                ->where('days_option', 'limited')
                ->whereDate('expire_date', $targetDate)
                ->get();

            foreach ($transactions as $trx) {
                // Cek idempotent — sudah pernah kirim milestone ini?
                $sent = json_decode($trx->expiry_reminder_sent ?? '{}', true);
                if (!empty($sent[$milestone])) continue;

                $owner = $trx->merchant->owner ?? null;
                if (!$owner) continue;

                $customerEmail = $owner->email;
                $customerPhone = $owner->phone;
                $customerName  = $owner->name;
                $packageName   = $trx->package->name ?? 'Paket';
                $expireDate    = Carbon::parse($trx->expire_date)->format('d F Y');
                $sisaHari      = $days > 0 ? $days : 0;
                $invoiceNo     = $trx->invoice ?? '-';
                $renewUrl      = 'https://chat.replai.id/app/starter/business/list';

                // Template variables
                $vars = [
                    '{name}'            => $customerName,
                    '{nama_paket}'      => $packageName,
                    '{tanggal_expire}'  => $expireDate,
                    '{sisa_hari}'       => $sisaHari,
                    '{link_perpanjang}' => $renewUrl,
                    '{no_invoice}'      => $invoiceNo,
                ];

                $isExpired = ($milestone === 'expired');

                // === Kirim Email ===
                $emailCol       = $isExpired ? 'email_expired_reminder'  : 'email_expiry_reminder';
                $emailTplCol    = $isExpired ? 'email_expired_reminder_template' : 'email_expiry_reminder_template';

                if ($setting->$emailCol === 'yes' && $customerEmail) {
                    $tpl = $setting->$emailTplCol
                        ? MessageTemplate::find($setting->$emailTplCol)
                        : null;
                    if ($tpl) {
                        $html = str_replace(array_keys($vars), array_values($vars), $tpl->html ?? '');
                        try {
                            $this->sendEmail($customerEmail, $html, $tpl);
                        } catch (\Throwable $e) {
                            Log::error("ExpiryReminderJob email error [{$milestone}] trx {$trx->id}: " . $e->getMessage());
                        }
                    }
                }

                // === Kirim WA ===
                $waCol    = $isExpired ? 'whatsapp_expired_reminder'  : 'whatsapp_expiry_reminder';
                $waTplCol = $isExpired ? 'whatsapp_expired_reminder_template' : 'whatsapp_expiry_reminder_template';

                if ($setting->$waCol === 'yes' && $customerPhone) {
                    $waTpl = $setting->$waTplCol
                        ? MessageTemplate::find($setting->$waTplCol)
                        : null;
                    if ($waTpl && $setting->device_notification) {
                        $msg = str_replace(array_keys($vars), array_values($vars), $waTpl->message ?? '');
                        try {
                            $waService = app(WhatsappServiceObserver::class);
                            $waService->sendMessage(
                                $customerPhone,
                                $setting->device_notification,
                                $msg,
                                '',
                                'text',
                                []
                            );
                        } catch (\Throwable $e) {
                            Log::error("ExpiryReminderJob WA error [{$milestone}] trx {$trx->id}: " . $e->getMessage());
                        }
                    }
                }

                // Mark milestone sebagai sudah terkirim (idempotent)
                $sent[$milestone] = true;
                $trx->update(['expiry_reminder_sent' => json_encode($sent)]);

                Log::info("ExpiryReminderJob sent [{$milestone}] to {$customerEmail} / {$customerPhone} for trx {$trx->id}");
            }
        }
    }

    /**
     * Kirim email via SMTP Brevo dari DB settings
     */
    private function sendEmail(string $toEmail, string $html, MessageTemplate $tpl): void
    {
        $emailSettings = Setting::where('merchant_id', null)
            ->first(['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name']);

        if ($emailSettings) {
            Config::set('mail.mailers.smtp.host',       $emailSettings->mail_host);
            Config::set('mail.mailers.smtp.port',       $emailSettings->mail_port);
            Config::set('mail.mailers.smtp.username',   $emailSettings->mail_username);
            Config::set('mail.mailers.smtp.password',   $emailSettings->mail_password);
            Config::set('mail.mailers.smtp.encryption', $emailSettings->mail_encryption);
            Config::set('mail.from.address',            $emailSettings->mail_from_address);
            Config::set('mail.from.name',               $emailSettings->mail_from_name);
        }

        Mail::to($toEmail)->send(new NotificationEmail($html, $tpl));
    }
}
