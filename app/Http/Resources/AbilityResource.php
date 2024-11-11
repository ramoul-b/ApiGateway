<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AbilityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'permission_id' => $this->permission_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
