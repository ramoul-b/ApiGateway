<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
 
    public function toArray($request)
    {
        return $this->collection->map(function ($permission) {
            return new PermissionResource($permission);
        })->toArray();
    }
    
    public function with($request)
    {
        return [
            'links' => [
                'self' => route('roles.index'),
            ],
        ];
    }

}
