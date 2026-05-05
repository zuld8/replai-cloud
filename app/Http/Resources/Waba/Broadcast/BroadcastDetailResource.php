<?php

namespace App\Http\Resources\Waba\Broadcast;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BroadcastDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'          => $this->name,
            'category'      => $this->category->id ?? '',
            'template'      => $this->template->id ?? '',
            'schedule'      => $this->schedule->format('Y-m-d H:i:s'),
            'delay'         => (int)$this->delay,
            'files'         => null,
            'stop_sending'  => (int)$this->stop_sending,
            'rest_sending'  => (int)$this->rest_sending,
            'devices'           => device_array(explode(",", $this->devices)),
            'whatsapp_sender'   => $this->whatsapp_sender_notif,
            'metadata'          => json_decode($this->metadata, true)
        ];
    }
}
