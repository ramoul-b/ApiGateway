<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id, 
            'user_id' => $this->user_id,
            'role' => new RoleResource($this->whenLoaded('role')),
            'default' => (bool)$this->default,
            'using' => $this->using,
            'created_at' => $this->created_at, 
            'updated_at' => $this->updated_at, 
        ];
    }
}
