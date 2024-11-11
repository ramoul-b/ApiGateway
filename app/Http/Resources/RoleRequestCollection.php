<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleRequestCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($roleRequest) {
            return new RoleRequestResource($roleRequest);
        })->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('role-requests.index'),
            ],
        ];
    }
}
