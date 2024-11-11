<?php

// MicroserviceCollection.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MicroserviceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($microservice) {
            return new MicroserviceResource($microservice);
        })->toArray();
    }
    
    public function with($request)
    {
        return [
            'links' => [
                'self' => route('microservices.index'),
            ],
        ];
    }
}
