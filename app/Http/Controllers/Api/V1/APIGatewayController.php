<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\PermissionService;
use Illuminate\Http\Request;
use App\Models\Microservice;
use App\Models\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiService;

class APIGatewayController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function handleRequest(Request $request, $service, $endpoint)
    {
        try {
            $microservice = Microservice::where('name', $service)->firstOrFail();
            $api = Api::where('route_in', $endpoint)->where('microservice_id', $microservice->id)->firstOrFail();

            if (Auth::check()) {
                // L'utilisateur est authentifié, vérifiez les autorisations
                if (!$this->permissionService->hasPermission(Auth::user(), $api->id)) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
            } else {
                // L'utilisateur n'est pas authentifié, retournez une erreur d'authentification
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $url = $this->buildMicroserviceUrl($microservice, $endpoint);
            $requestData = $this->adaptRequestData($request);

            // Transférez la requête au microservice
            $response = Http::withHeaders($request->headers->all())->send($request->method(), $url, $requestData);

            // Retournez directement la réponse JSON du microservice
            return response()->json(json_decode($response->body()), $response->status());
        } catch (\Exception $e) {
            // Utilisez ApiService pour gérer uniformément les exceptions
            return ApiService::handleException($e);
        }
    }
    
    protected function buildMicroserviceUrl($microservice, $endpoint)
    {
        return "http://{$microservice->main_ipv4}/{$endpoint}";
    }

    protected function adaptRequestData(Request $request)
    {
        $contentType = $request->header('Content-Type');

        // Pour les requêtes JSON
        if (str_contains($contentType, 'application/json')) {
            return ['json' => $request->json()->all()];
        }
        // Pour les requêtes avec données de formulaire
        elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return ['form_params' => $request->input()];
        }

        return [];
    }
}
