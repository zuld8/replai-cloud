<?php

namespace App\Mail\User;

use App\Models\InternalSetting;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ForgetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */

    public $user;
    public $token;
    public function __construct(User $user, String $token)
    {
        $this->user         = $user;
        $this->token        = $token;
        $this->setEmailConfig();
    }

    /**
     * Set dynamic email configuration
     */
    protected function setEmailConfig(): void
    {
        // Get master admin email settings
        $emailSettings = Setting::where("merchant_id", null)->first(['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name']);
 
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

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $setting = InternalSetting::first(['logo', 'app_name']);
        return new Content(
            view: 'mail.user.password',
            with: [
                'setting' => $setting
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
