<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Services\ApiService;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        //check on same api call not exist json request
        //return $request->expectsJson() ? null : route('login');
        //force on all api call to response json
        return ApiService::response([
            'message' => 'Token is expired'
        ], 401);
    }
}
