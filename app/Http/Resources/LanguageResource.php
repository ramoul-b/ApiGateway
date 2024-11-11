<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray($request)
    {
        // Assuming 'flag' is the correct field name for the flag image.
        // Add additional fields as necessary.
        return [
            'id' => $this->id,
            'name' => $this->name,
            'iso_639_code' => $this->iso_639_code,
            'flag' => $this->flag,
            'files' => LanguageFileResource::collection($this->whenLoaded('files')),
        ];
    }
}
