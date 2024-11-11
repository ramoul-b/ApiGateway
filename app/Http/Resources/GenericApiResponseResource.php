<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenericApiResponseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'data' => $this->resource,
        ];
    }
}
