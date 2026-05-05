<?php

namespace App\Http\Resources\Kanban;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'tag'                       => $this->tag,
            'position'                  => $this->position,
            'color'                     => $this->color,
            'is_default'                => $this->is_default,
            'is_closeable'              => $this->is_closeable,
            'is_default'                => $this->is_default
        ];
    }
}
