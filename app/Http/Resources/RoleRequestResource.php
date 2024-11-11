<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleRequestResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->whenLoaded('role')),
            'account_id' => $this->account_id,
            //'account' => new AccountResource($this->whenLoaded('account')),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
