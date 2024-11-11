<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            return new PermissionCategoryResource($category);
        })->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('permission_categories.index'),
            ],
        ];
    }
}
