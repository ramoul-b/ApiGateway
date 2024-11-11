<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LanguageFileCollection extends ResourceCollection
{
    public function toArray($request)
    {
        
        return $this->collection->mapInto(LanguageFileResource::class)->toArray();
    }

}
