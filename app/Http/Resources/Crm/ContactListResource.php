<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $date = '';
        if ($this->last_message) {
            $msgDate = $this->last_message->created_at;
            if ($msgDate->isToday()) {
                $date = $msgDate->format('H:i');
            } else {
                $date = $msgDate->format('n/j/y h:i A');
            }
        }

        $device     = $this->device->name ?? null;

        if ($this->from == 'waba') {
            // Show WABA meta account name (e.g. "Roswita - Admin Yayasan Golden Future")
            $waba = $this->waba;
            if ($waba && $waba->meta) {
                $device = $waba->meta->name;
            } else {
                $device = $waba->phone ?? null;
            }
        }

        if ($this->from == 'messanger') {
            // Show Fanpage name (e.g. "Replai Automation")
            $device = $this->messenger->page_name ?? null;
        }

        return [
            'id'            => $this->id,
            'name'          => $this->name ?? $this->from_number,
            'status'        => $this->status,
            'from'          => $this->from,
            'phone'         => $this->from_number,
            'bsuid'         => $this->bsuid,
            'wa_username'   => $this->wa_username,
            'device'        => $device,
            'livechat'      => $this->livechat->name ?? '',
            'telegram'      => $this->telegram->name ?? '',
            'is_assignment' => $this->data_assignment,
            'not_read'      => $this->unread_count, // FIX: pakai kolom langsung, tidak perlu query details
            'photo'         => $this->image_data,
            'last_message'  => array(
                'message'       => $this->getPreviewMessage(),
                'time'          => $this->last_message
                    ? ($this->last_message->created_at->isToday()
                        ? $this->last_message->created_at->format('H:i')
                        : $this->last_message->created_at->format('n/j/y h:i A'))
                    : '',
                'date'          => $date,
            )
        ];
    }

    /**
     * Get preview message for sidebar — fallback for media types
     */
    private function getPreviewMessage(): string
    {
        $last = $this->last_message;
        if (!$last) return '';

        $msg = $last->message ?? '';
        $type = $last->type ?? 'text';

        // If message is not empty, use it (could be caption)
        if (!empty(trim($msg)) && $type !== 'template') {
            return $msg;
        }

        // Fallback for media types
        return match($type) {
            'image'    => '📷 Photo',
            'video'    => '🎥 Video',
            'audio'    => '🎵 Audio',
            'document' => '📄 Document',
            'sticker'  => '🏷️ Sticker',
            'template' => '📋 ' . ($msg ?: 'Template'),
            default    => $msg,
        };
    }
}
