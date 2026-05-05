<?php

namespace App\Mail;

use App\Models\Blash\BlashDetail;
use App\Models\Store\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreMessageEmail extends Mailable
{

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $store;
    public $details;
    public function __construct(Store $store, BlashDetail $details)
    {
        $this->store        = $store;
        $this->details      = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->details->parent->name ?? 'Promosi Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.promotions',
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
