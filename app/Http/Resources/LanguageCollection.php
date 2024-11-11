<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LanguageCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->mapInto(LanguageResource::class)->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('languages.index'),
            ],
        ];
    }
}
