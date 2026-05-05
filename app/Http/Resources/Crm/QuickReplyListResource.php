<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickReplyListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'content'           => $this->content,
            'type'              => $this->type,
            'file_type'         => $this->file_type,
            'file_name'         => $this->file_name,
            'file_size'         => $this->file_name,
            'media_url'         => $this->media_url ? asset($this->media_url) : null
        ];
    }
}
