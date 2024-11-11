<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageFileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'language_id' => $this->language_id,
            'path_file' => $this->path_file,
            'type' => $this->type,
            'md5_path_file' => $this->md5_path_file,
        ];
    }
}
