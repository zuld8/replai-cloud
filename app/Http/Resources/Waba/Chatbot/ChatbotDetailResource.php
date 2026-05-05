<?php

namespace App\Http\Resources\Waba\Chatbot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatbotDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'keyword'       => $this->keyword,
            'method'        => $this->reply_method,
            'template'      => $this->template->id ?? '',
            'devices'       => device_array(explode(",", $this->select_device)),
            'metadata'      => json_decode($this->metadata, true)
        ];
    }
}
