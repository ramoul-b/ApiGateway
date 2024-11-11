<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Services\ApiService; 
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler {
    public function render($request, Throwable $exception) {
        //if ($request->wantsJson()) {   
            Log::error($exception);

            $exception = $this->prepareException($exception);

            if ($exception instanceof AuthenticationException) {
                $exception = $this->unauthenticated($request, $exception);
            }

            if ($exception instanceof ValidationException) {
                $exception = $this->convertValidationExceptionToResponse($exception, $request);
            }

            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
            $message = method_exists($exception, 'getMessage') ? $exception->getMessage() : Response::$statusTexts[$statusCode];
            $errors = $exception instanceof ValidationException ? $exception->errors() : [];

            return ApiService::response([
                'message' => $message,
                'errors' => $errors
            ], $statusCode);
        //} else {
        //    return parent::render($request, $exception);
        //}
    }
}
