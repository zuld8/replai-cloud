<?php

namespace App\Mail;

use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Part\DataPart;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $htmlContent;
    public MessageTemplate $template;
    protected string $logoPath;
    protected string $logoCid = 'logo@replai.id';

    public function __construct(string $message, MessageTemplate $template)
    {
        $this->htmlContent = $message;
        $this->template    = $template;
        $this->logoPath    = public_path('img/logo_email.png');
        $this->setEmailConfig();
    }

    protected function setEmailConfig(): void
    {
        $s = Setting::where('merchant_id', null)
            ->first(['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name']);
        if ($s) {
            Config::set('mail.mailers.smtp.host',       $s->mail_host);
            Config::set('mail.mailers.smtp.port',       $s->mail_port);
            Config::set('mail.mailers.smtp.username',   $s->mail_username);
            Config::set('mail.mailers.smtp.password',   $s->mail_password);
            Config::set('mail.mailers.smtp.encryption', $s->mail_encryption);
            Config::set('mail.from.address',            $s->mail_from_address ?? 'noreply@replai.id');
            Config::set('mail.from.name',               $s->mail_from_name ?? 'Replai.id');
        }
    }

    public function build(): static
    {
        // Ganti placeholder {{LOGO_SRC}} dengan cid: reference
        $html = str_replace('{{LOGO_SRC}}', 'cid:' . $this->logoCid, $this->htmlContent);

        $mail = $this->subject($this->template->name)->html($html);

        // Embed logo sebagai inline CID attachment (bukan URL eksternal)
        // → selalu muncul tanpa user perlu klik "tampilkan gambar"
        if (file_exists($this->logoPath)) {
            $logoData = file_get_contents($this->logoPath);
            $logoCid  = $this->logoCid;

            $this->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($logoData, $logoCid) {
                $part = new DataPart($logoData, 'logo.png', 'image/png');
                $part->asInline();
                $part->setContentId($logoCid);
                $message->addPart($part);
            });
        }

        return $mail;
    }
}
