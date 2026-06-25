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
                $date = $msgDate->format('d/m/y H:i');
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
            // WABA 24h session chip — pesan MASUK terakhir dari pelanggan (ISO8601 +07:00)
            // Diset HANYA saat from='user' di HistoryChatDetail hook → akurat untuk window 24 jam
            // Beda dari last_message_at yang mencakup semua arah (pakai last_message_at buat urutan/waktu)
            'last_inbound_at' => $this->from === 'waba'
                ? ($this->last_inbound_at ? $this->last_inbound_at->toIso8601String() : null)
                : null,
            // WABA 24h session chip — waktu pesan terakhir (ISO format)
            // Vue pakai ini untuk hitung sisa 24 jam. Kalau bukan WABA = null.
            
            'name'          => $this->name ?? $this->from_number,
            'status'        => $this->status,
            'from'          => $this->from,
            'phone'         => $this->from_number,
            'bsuid'         => $this->bsuid,
            'wa_username'   => $this->wa_username,
            'device'        => $device,
            'livechat'      => $this->livechat->name ?? '',
            'telegram'      => $this->telegram->name ?? '',
            'instagram'     => $this->instagram->name ?? '',
            'is_assignment' => $this->data_assignment,
            'not_read'      => $this->unread_count, // FIX: pakai kolom langsung, tidak perlu query details
            'photo'         => $this->from === 'instagram'
                ? ($this->avatar_url ? asset($this->avatar_url) : null)
                : $this->image_data,
            'is_pinned'    => (bool) ($this->is_pinned_for_user ?? false),
            'is_archived'  => (bool) ($this->is_archived ?? false),
            'last_message'  => array(
                'message'       => $this->getPreviewMessage(),
                'time'          => $this->last_message
                    ? ($this->last_message->created_at->isToday()
                        ? $this->last_message->created_at->format('H:i')
                        : $this->last_message->created_at->format('d/m/y H:i'))
                    : '',
                'date'          => $date,
            ),
            // Labels: decode JSON column, return array of {id,name,color} objects
            // Data baru tersimpan sebagai array objek lengkap — no N+1 needed.
            'labels'        => collect(json_decode($this->label ?? '[]', true) ?: [])
                ->map(fn($l) => is_array($l) ? [
                    'id'    => $l['id']    ?? null,
                    'name'  => $l['name']  ?? '',
                    'color' => $l['color'] ?? '#888888',
                ] : ['id' => $l, 'name' => '', 'color' => '#888888']) // fallback for old id-only data
                ->filter(fn($l) => !empty($l['id']))
                ->values(),
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
