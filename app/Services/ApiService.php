<?php

namespace App\Services;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ApiService
{
    public static function response($data, $statusCode = 200)
    {

        $response = [];

        $locale = App::getLocale();

        if ($statusCode >= 200 && $statusCode < 300) {
            // Successful responses
            $response = is_null($data) ? ['message' => __('messages.success', [], $locale)] : $data;
        } elseif ($statusCode >= 400 && $statusCode < 500) {
            // Client error responses
            $response['message'] = __('messages.error', [], $locale);
            if ($statusCode==404) $response['message'] = __('messages.error_resource_not_found', [], $locale);
            $response['error'] = $data;
        } elseif ($statusCode >= 500 && $statusCode < 600) {
            // Server error responses
            //response data on debug mode
            if (config('app.debug')) {
                $response['error'] = $data;
            }
            else $response['message'] = __('messages.server_error', [], $locale);
            //LOG ERROR
            Log::error($data);
        }

        return response()->json($response, $statusCode);
    }
}
