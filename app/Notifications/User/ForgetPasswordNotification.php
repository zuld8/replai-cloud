<?php

namespace App\Notifications\User;

use App\Mail\User\ForgetPasswordEmail;
use Illuminate\Bus\Queueable; 
use Illuminate\Notifications\Notification;

class ForgetPasswordNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public $token;
    public function __construct($token)
    {
        $this->token    = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new ForgetPasswordEmail($notifiable, $this->token))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
