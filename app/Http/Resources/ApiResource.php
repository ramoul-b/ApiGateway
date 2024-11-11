<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_in' => $this->route_in,
            'method' => $this->method,
            'microservice' => new MicroserviceResource($this->microservice), 
            'permissions' => PermissionResource::collection($this->permissions), 
        ];
    }
}
