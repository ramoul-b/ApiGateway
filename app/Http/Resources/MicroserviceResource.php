<?php

// MicroserviceResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MicroserviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'secret_key' => $this->secret_key,
            'main_ipv4' => $this->main_ipv4,
            'load_balancer_ipv4' => $this->load_balancer_ipv4,
            'main_ipv6' => $this->main_ipv6,
            'load_balancer_ipv6' => $this->load_balancer_ipv6,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

