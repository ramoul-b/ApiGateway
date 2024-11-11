<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\AccountResource;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'id' => $this->id, //Gate::allows('AG_tests_showfieldid') ? $this->id : null,
            'name' => $this->name,
            'surname' => $this->surname,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'role' => RoleResource::collection($this->whenLoaded('role')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
