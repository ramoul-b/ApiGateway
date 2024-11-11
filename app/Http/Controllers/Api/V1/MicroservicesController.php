<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Microservice; // Assurez-vous que le modèle Microservice existe
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MicroserviceResource;
use App\Services\ApiService;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;


/**
 * @OA\Tag(
 *     name="Microservices",
 *     description="Microservices management"
 * )
 */
class MicroservicesController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/v1/microservices",
     *     tags={"Microservices"},
     *     summary="List all Microservices",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     */
    public function index()
    {
        try {
            $microservices = MicroserviceResource::collection(Microservice::all());
            return ApiService::response($microservices);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.error_retrieving_microservices')], 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/api/v1/microservices",
     *     tags={"Microservices"},
     *     summary="Create a new Microservice",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="secret_key", type="string"),
     *             @OA\Property(property="main_ipv4", type="string"),
     *             @OA\Property(property="load_balancer_ipv4", type="string"),
     *             @OA\Property(property="main_ipv6", type="string"),
     *             @OA\Property(property="load_balancer_ipv6", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Microservice created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Afficher les données reçues depuis la requête dans les logs
        //Log::error('Request data: ', $request->all());
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'secret_key' => 'required|string|max:255',
            'main_ipv4' => 'nullable|ip',
            'load_balancer_ipv4' => 'nullable|ip',
            'main_ipv6' => 'nullable|ipv6',
            'load_balancer_ipv6' => 'nullable|ipv6',
        ]);
    
        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }
    
        try {
            $microservice = Microservice::create($request->all());
            return ApiService::response($microservice, 201);
        } catch (\Exception $e) {
            // Enregistrer l'erreur dans les logs
            //Log::error('Microservice creation failed: ' . $e->getMessage());
            return ApiService::response(['error' => __('messages.microservice_creation_failed')], 500);
        }
    }


        /**
     * @OA\Get(
     *     path="/api/v1/microservices/{id}",
     *     tags={"Microservices"},
     *     summary="Get Microservice by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $microservice = Microservice::findOrFail($id);
            return ApiService::response($microservice);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.microservice_not_found')], 404);
        }
    }

        /**
     * @OA\Put(
     *     path="/api/v1/microservices/{id}",
     *     tags={"Microservices"},
     *     summary="Update an existing Microservice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="secret_key", type="string"),
     *             @OA\Property(property="main_ipv4", type="string"),
     *             @OA\Property(property="load_balancer_ipv4", type="string"),
     *             @OA\Property(property="main_ipv6", type="string"),
     *             @OA\Property(property="load_balancer_ipv6", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Microservice updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $microservice = Microservice::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'secret_key' => 'string|max:255',
                'main_ipv4' => 'nullable|ip',
                'load_balancer_ipv4' => 'nullable|ip',
                'main_ipv6' => 'nullable|ipv6',
                'load_balancer_ipv6' => 'nullable|ipv6',
            ]);

            if ($validator->fails()) {
                return ApiService::response($validator->errors(), 422);
            }

            $microservice->update($request->all());
            return ApiService::response($microservice);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.microservice_update_failed')], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/microservices/{id}",
     *     tags={"Microservices"},
     *     summary="Delete a Microservice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Microservice deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            Microservice::destroy($id);
            return ApiService::response(['message' => __('messages.microservice_deleted_success')]);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.microservice_deletion_failed')], 500);
        }
    }
}
