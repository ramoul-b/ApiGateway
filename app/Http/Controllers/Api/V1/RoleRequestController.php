<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Role;
use App\Models\RoleRequest;
use App\Services\ApiService;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleRequestResource;
use App\Http\Resources\RoleRequestCollection;

class RoleRequestController extends Controller
{
/**
 * List all role requests.
 * 
 * @OA\Get(
 *     path="/api/v1/role-requests",
 *     summary="List all role requests",
 *     tags={"RoleRequests"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="A list of role requests."
 *     )
 * )
 */
public function index()
{
    try {
        $roleRequests = RoleRequestResource::collection(RoleRequest::paginate());
        return  ApiService::response($roleRequests);
    } catch (\Exception $e) {
        // Log the error
        Log::error('Failed to retrieve role requests: ' . $e->getMessage());
        return response()->json(['error' => __('messages.error_retrieving_role_requests')], 500);
    }
}


/**
 * Create a new role request.
 *
 * @OA\Post(
 *     path="/api/v1/role-requests",
 *     summary="Create a new role request",
 *     tags={"RoleRequests"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="role_id", type="integer", description="The ID of the role"),
 *             @OA\Property(property="account_id", type="integer", description="The ID of the account organization"),
 *             @OA\Property(property="status", type="string", description="The status of the role request", enum={"waiting", "denied", "assigned"}),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Role request created successfully."
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input."
 *     )
 * )
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'role_id' => 'required|integer|exists:roles,id',
        'account_id' => 'required|integer|exists:accounts,id',
        'status' => 'required|in:waiting,denied,assigned',
    ]);


    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }
    try {
        $roleRequest = RoleRequest::create($validator->validated());
        return new RoleRequestResource($roleRequest);
    } catch (\Exception $e) {
        Log::error('Error creating role request: ' . $e->getMessage());
        return ApiService::response(['error' => __('messages.role_request_creation_failed')], 500);

    }
}




/**
 * Show the specified role request by ID.
 *
 * @OA\Get(
 *     path="/api/v1/role-requests/{id}",
 *     summary="Get a specific role request",
 *     tags={"RoleRequests"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         description="Role request ID",
 *         required=true,
 *         in="path",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role request retrieved successfully.",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role request not found."
 *     )
 * )
 */
public function show($id)
{
    try {
        $roleRequest = RoleRequest::findOrFail($id);
        return new RoleRequestResource($roleRequest);
    } catch (\Exception $e) {
        return ApiService::response(['error' => __('messages.role_request_not_found')], 404);
    }
}




    /**
     * Update the specified role request.
     * 
     * @OA\Put(
     *     path="/api/v1/role-requests/{id}",
     *     summary="Update a role request",
     *     tags={"RoleRequests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         description="Role request ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="The status of the role request", enum={"waiting", "denied", "assigned"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role request updated successfully.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role request not found."
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $roleRequest = RoleRequest::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'role_id' => 'sometimes|required|integer|exists:roles,id',
                'account_id' => 'sometimes|required|integer|exists:accounts,id',
                'status' => 'required|in:waiting,denied,assigned',
            ]);
    
            if ($validator->fails()) {
                return ApiService::response($validator->errors(), 422);
            }
    
            $roleRequest->update($validator->validated());
            return new RoleRequestResource($roleRequest);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'RoleRequest not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating role request: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    
    

    /**
     * Remove the specified role request from storage.
     * 
     * @OA\Delete(
     *     path="/api/v1/role-requests/{id}",
     *     summary="Delete a role request",
     *     tags={"RoleRequests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         description="Role request ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Role request deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role request not found."
     *     )
     * )
     */
        public function destroy(string $id)
    {
        try {
            $roleRequest = RoleRequest::findOrFail($id);
            $roleRequest->delete();
            return ApiService::response(['message' => __('messages.role_request_deleted_success')], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'RoleRequest not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting role request: ' . $e->getMessage());
            return ApiService::response(['error' => __('messages.role_request_deletion_failed')], 500);
        }
    }


       /**
 * @OA\Post(
 *     path="/api/v1/roles/request/{roleCode}",
 *     summary="Create a role request",
 *     tags={"RoleRequests"},
 *     @OA\Parameter(
 *         name="roleCode",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         ),
 *         description="The code of the role to request",
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role request created successfully",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found"
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
public function createRequest($roleCode, Request $request)
{
    Log::info("Attempting to create role request for roleCode: {$roleCode}");
    try {
        $role = Role::where('code', $roleCode)->where('requestable', 1)->firstOrFail();
        Log::info("Found role: {$role->id}");

        $account = auth()->user()->accounts()->where('using', 1)->firstOrFail();
        Log::info("Found account: {$account->id}");

        $roleRequest = RoleRequest::create([
            'role_id' => $role->id,
            'account_id' => $account->id,
            'status' => 'waiting',
        ]);

        return new RoleRequestResource($roleRequest);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        if ($role = Role::where('code', $roleCode)->first()) {
            // Le rôle existe mais n'est pas requestable
            Log::error("Role found but is not requestable: {$role->id}");
            return response()->json(['error' => 'Role is not requestable.'], 403);
        }
        // Ni le rôle ni le compte n'ont été trouvés
        Log::error("Role or account not found: " . $e->getMessage());
        return response()->json(['error' => 'Role or account not found.'], 404);
    } catch (\Exception $e) {
        Log::error('Error creating role request: ' . $e->getMessage());
        return response()->json(['error' => __('messages.role_request_creation_failed')], 500);
    }
}



    /**
 * @OA\Get(
 *     path="/api/v1/roles/request/{roleRequestId}/approve",
 *     summary="Approve a role request",
 *     tags={"RoleRequests"},
 *     @OA\Parameter(
 *         name="roleRequestId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         ),
 *         description="The ID of the role request to approve",
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role request approved successfully",
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - User does not have permission to approve this request"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role request not found"
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
public function approveRequest($roleRequestId)
{
    try {
        $roleRequest = RoleRequest::findOrFail($roleRequestId);
        $roleRequest->update(['status' => 'assigned']);

        return new RoleRequestResource($roleRequest);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Role request not found.'], 404);
    } catch (\Exception $e) {
        Log::error('Error approving role request: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to approve role request.'], 500);
    }
}

    /**
     * @OA\Get(
     *     path="/api/v1/roles/request/{roleRequestId}/deny",
     *     summary="Deny a role request",
     *     tags={"RoleRequests"},
     *     @OA\Parameter(
     *         name="roleRequestId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="The ID of the role request to deny",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role request denied successfully",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have permission to deny this request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role request not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function denyRequest($roleRequestId)
    {
        try {
            $roleRequest = RoleRequest::findOrFail($roleRequestId);
            $roleRequest->update(['status' => 'denied']);
    
            return new RoleRequestResource($roleRequest);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Role request not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error denying role request: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to deny role request.'], 500);
        }
    }
    




}
