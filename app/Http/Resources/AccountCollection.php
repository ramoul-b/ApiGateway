<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AccountCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($account) {
            return new AccountResource($account);
        })->toArray();
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('accounts.index'),
            ],
        ];
    }
}
