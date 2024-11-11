<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AbilityCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($ability) {
            return new AccountResource($ability);
        })->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('abilities.index'),
            ],
        ];
    }
}
