<?php

namespace App\Notifications;

use App\Mail\StoreMessageEmail;
use App\Models\Blash\BlashDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StoreMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $details;
    public function __construct(BlashDetail $details)
    {
        $this->details      = $details;
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
        return (new StoreMessageEmail($notifiable, $this->details))->to($notifiable->email);
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
