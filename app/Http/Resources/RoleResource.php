<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'organization_id' => $this->organization_id,
            'organization_address_id' => $this->organization_address_id,
            'requestable' => $this->requestable,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
