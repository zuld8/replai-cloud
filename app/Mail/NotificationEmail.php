<?php

namespace App\Mail;

use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $message;
    public $template;
    public function __construct($message, MessageTemplate $template)
    {
        $this->message      = $message;
        $this->template     = $template;
        $this->setEmailConfig();
    }

    /**
     * Set SMTP config dari DB settings (Brevo), bukan dari .env statis.
     */
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
            Config::set('mail.from.address',            $s->mail_from_address);
            Config::set('mail.from.name',               $s->mail_from_name);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.notification',
            with: [
                'messageContent' => $this->message
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
