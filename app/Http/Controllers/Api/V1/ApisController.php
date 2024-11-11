<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ApiResource; 
use App\Services\ApiService;
use App\Models\ApiCondition;
/**
 * @OA\Tag(
 *     name="APIs",
 *     description="APIs management"
 * )
 */
class ApisController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/v1/apis",
 *     tags={"APIs"},
 *     summary="List all APIs",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="microservice_id", type="integer"),
 *             @OA\Property(property="route_in", type="string"),
 *             @OA\Property(property="method", type="string"),
 *         ))
 *     )
 * )
 */
public function index()
{
    try {
        $apis = ApiResource::collection(Api::with(['microservice', 'permissions'])->get());
        return ApiService::response($apis);
    } catch (\Exception $e) {
        \Log::error("Failed to retrieve APIs: " . $e->getMessage());
        return ApiService::response(['error' => __('messages.error_retrieving_apis')], 500);
    }
}



/**
 * @OA\Post(
 *     path="/api/v1/apis",
 *     tags={"APIs"},
 *     summary="Create a new API",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="microservice_id", type="integer"),
 *             @OA\Property(property="route_in", type="string"),
 *             @OA\Property(property="method", type="string"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="API created successfully"
 *     )
 * )
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'microservice_id' => 'required|exists:microservices,id',
        'route_in' => 'required|string|max:255',
        'method' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    try {
        $api = Api::create($request->all());
        return ApiService::response(new ApiResource($api), 201);
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.api_creation_failed')], 500);
    }
}

/**
 * @OA\Get(
 *     path="/api/v1/apis/{id}",
 *     tags={"APIs"},
 *     summary="Get API by ID",
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
        $api = Api::with(['microservice', 'permissions'])->findOrFail($id);
        return ApiService::response(new ApiResource($api));
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.api_not_found')], 404);
    }
}


/**
 * @OA\Put(
 *     path="/api/v1/apis/{id}",
 *     tags={"APIs"},
 *     summary="Update an API by ID",
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
 *             @OA\Property(property="microservice_id", type="integer"),
 *             @OA\Property(property="route_in", type="string"),
 *             @OA\Property(property="method", type="string"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="API updated successfully"
 *     )
 * )
 */
public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'microservice_id' => 'exists:microservices,id',
        'route_in' => 'string|max:255',
        'method' => 'string|max:255',
    ]);

    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    try {
        $api = Api::findOrFail($id);
        $api->update($request->all());
        return ApiService::response(new ApiResource($api));
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.api_update_failed')], 500);
    }
}

/**
 * @OA\Delete(
 *     path="/api/v1/apis/{id}",
 *     tags={"APIs"},
 *     summary="Delete an API by ID",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="API deleted successfully"
 *     )
 * )
 */
public function destroy($id)
{
    try {
        $api = Api::findOrFail($id);
        $api->delete();
        return ApiService::response(['message' => __('messages.api_deleted_success')]);
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.api_deletion_failed')], 500);
    }
}
/**
 * Get API Conditions
 *
 * @OA\Get(
 *     path="/api/v1/apiconditions/{api_id}",
 *     tags={"API Conditions"},
 *     summary="Get API Conditions by API ID",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="api_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="api_id", type="integer"),
 *             @OA\Property(property="permission_id", type="integer"),
 *             @OA\Property(property="condition_object", type="string"),
 *             @OA\Property(property="condition", type="string"),
 *             @OA\Property(property="condition_level", type="string"),
 *             @OA\Property(property="level", type="integer"),
 *             @OA\Property(property="position_level", type="integer"),
 *         ))
 *     )
 * )
 */
public function getApiConditions($api_id)
{
    try {
        $apiConditions = ApiCondition::where('api_id', $api_id)->get();
        return ApiService::response($apiConditions);
    } catch (\Exception $e) {
        \Log::error("Failed to retrieve API conditions: " . $e->getMessage());
        return ApiService::response(['error' => __('messages.error_retrieving_api_conditions')], 500);
    }
}
/**
 * Store API Conditions
 *
 * @OA\Post(
 *     path="/api/v1/apiconditions/{api_id}",
 *     tags={"API Conditions"},
 *     summary="Create API Conditions for API",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="api_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"permission_id", "condition_object", "level", "position_level"},
 *             @OA\Property(property="permission_id", type="integer"),
 *             @OA\Property(property="condition_object", type="string", enum={"hasPermission", "hasNotPermission"}),
 *             @OA\Property(property="condition", type="string", enum={"AND", "OR", "null"}),
 *             @OA\Property(property="condition_level", type="string", enum={"AND", "OR", "null"}),
 *             @OA\Property(property="level", type="integer"),
 *             @OA\Property(property="position_level", type="integer"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="API Conditions created successfully"
 *     )
 * )
 */

public function storeApiConditions(Request $request, $api_id)
{
    $validator = Validator::make($request->all(), [
        'permission_id' => 'required|integer',
        'condition_object' => 'required|in:hasPermission,hasNotPermission',
        'condition' => 'nullable|in:AND,OR|required_with:position_level',
        'condition_level' => 'nullable|in:AND,OR|required_when:level,>0|required_with:position_level',
        'level' => 'required|integer',
        'position_level' => 'required|integer|required_without_all:condition,condition_level',
    ]);
    
    $validator->sometimes('condition_level', 'nullable', function ($input) {
        return $input->level > 0 && $input->position_level == 0;
    });

    $validator->sometimes('condition', 'nullable', function ($input) {
        return $input->position_level > 0;
    });


    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    try {
        // Créer les API Conditions
        $apiConditions = [
            [
                'api_id' => $api_id,
                'permission_id' => $request->input('permission_id'),
                'condition_object' => $request->input('condition_object'),
                'condition' => $request->input('condition'),
                'condition_level' => $request->input('condition_level'),
                'level' => $request->input('level'),
                'position_level' => $request->input('position_level'),
            ]
        ];

        ApiCondition::insert($apiConditions);

        return ApiService::response(['message' => __('messages.api_conditions_created_success')], 201);
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.api_conditions_creation_failed')], 500);
    }
}




/**
 * Update API Conditions
 *
 * @OA\Put(
 *     path="/api/v1/apiconditions/{api_id}",
 *     tags={"API Conditions"},
 *     summary="Update API Conditions for API",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="api_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"conditions"},
 *             @OA\Property(
 *                 property="conditions",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"id", "permission_id", "condition_object", "level", "position_level"},
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="permission_id", type="integer"),
 *                     @OA\Property(property="condition_object", type="string", enum={"hasPermission", "hasNotPermission"}),
 *                     @OA\Property(property="condition", type="string", enum={"AND", "OR", "null"}),
 *                     @OA\Property(property="condition_level", type="string", enum={"AND", "OR", "null"}),
 *                     @OA\Property(property="level", type="integer"),
 *                     @OA\Property(property="position_level", type="integer"),
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="API conditions updated successfully",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity. Validation error.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="errors", type="object", description="Validation errors"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error. Failed to update API conditions.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message"),
 *         ),
 *     ),
 * )
 */


 public function updateApiConditions(Request $request, $api_id)
 {
     // Validation des données de requête
     $validator = Validator::make($request->all(), [
    'conditions.*.id' => 'required|exists:api_conditions,id',
    'conditions.*.permission_id' => 'required|integer',
    'conditions.*.condition_object' => 'required|in:hasPermission,hasNotPermission',
    'conditions.*.condition' => 'nullable|in:AND,OR|required_with:conditions.*.position_level',
    'conditions.*.condition_level' => 'nullable|in:AND,OR|required_when:conditions.*.level,>0|required_with:conditions.*.position_level',
    'conditions.*.level' => 'required|integer',
    'conditions.*.position_level' => 'required|integer|required_without_all:conditions.*.condition,conditions.*.condition_level',
]);
 

$validator->sometimes('conditions.*.condition_level', 'nullable', function ($input) {
    return $input->level > 0 && $input->position_level == 0;
});


$validator->sometimes('conditions.*.condition', 'nullable', function ($input) {
    return $input->position_level > 0;
});

     if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
     }
 
     try {
         // Récupérer l'API correspondante
         $api = Api::findOrFail($api_id);

         $api->permissions()->delete();


         $apiConditionsData = $request->input('conditions');

         foreach ($apiConditionsData as $conditionData) {
        $api->permissions()->create([
            'permission_id' => $conditionData['permission_id'],
            'condition_object' => $conditionData['condition_object'],
            'condition' => $conditionData['condition'],
            'condition_level' => $conditionData['condition_level'],
            'level' => $conditionData['level'],
            'position_level' => $conditionData['position_level'],
        ]);
    }
     
         // Mettre à jour les conditions API en utilisant sync()
        // $api->permissions()->sync($conditionsToSync);
     
         return response()->json(['message' => 'API conditions updated successfully'], 200);
     } catch (\Exception $e) {
         \Log::error("Failed to update API conditions: " . $e->getMessage());
         return response()->json(['error' => 'Failed to update API conditions'], 500);
     }   
 }

/**
 * Delete API Conditions
 *
 * @OA\Delete(
 *     path="/api/v1/apiconditions/{api_id}",
 *     tags={"API Conditions"},
 *     summary="Delete API Conditions for API",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="api_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="API conditions deleted successfully",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error. Failed to delete API conditions.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message"),
 *         ),
 *     ),
 * )
 */
 
public function deleteApiConditions($api_id)
{
    try {
        // Récupérer l'API correspondante
        $api = Api::findOrFail($api_id);

        // Supprimer toutes les conditions API associées à cette API
        $api->permissions()->delete();

        return response()->json(['message' => 'API conditions deleted successfully'], 200);
    } catch (\Exception $e) {
        \Log::error("Failed to delete API conditions: " . $e->getMessage());
        return response()->json(['error' => 'Failed to delete API conditions'], 500);
    }
}



}
