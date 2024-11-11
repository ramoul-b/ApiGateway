<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($api) {
            return new ApiResource($api);
        })->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('apis.index'),
            ],
        ];
    }
}
