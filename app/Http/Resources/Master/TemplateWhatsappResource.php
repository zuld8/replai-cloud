<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateWhatsappResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $bodyData                   = json_decode($this->button_or_list, true);
        return [
            "name"                  => $this->name,
            "type_content"          => $this->type_content,
            "media_type"            => $this->media_type ?? '',
            "body_message"          => $this->message ?? '',
            "media"                 => [
                "type"                  => $this->media_type ?? '',
                "url"                   => $this->image != null ? asset($this->image) : null
            ],
            "footer_message"        => $bodyData != null && isset($bodyData['footer']) ? $bodyData['footer'] : '',
            "image"                 => '',
            "lang"                  => $bodyData != null && isset($bodyData['latitude']) ? $bodyData['latitude'] : '',
            "long"                  => $bodyData != null && isset($bodyData['longitude']) ? $bodyData['longitude'] : '',
            "buttons"               => $bodyData != null && isset($bodyData['buttons']) ? $bodyData['buttons'] : '',
            "options"               => $bodyData != null && isset($bodyData['options']) ? $bodyData['options'] : '',
            "list"                  => array(
                "title"                 => $bodyData != null && isset($bodyData['title']) ? $bodyData['title'] : '',
                "button_name"           => $bodyData != null && isset($bodyData['button_name']) ? $bodyData['button_name'] : '',
                "sections"              => $bodyData != null && isset($bodyData['sections']) ? $bodyData['sections'] : [],
            )
        ];
    }
}
