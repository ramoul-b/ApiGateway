<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiService;
use App\Http\Resources\PermissionResource;


class PermissionsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/permissions",
     *     tags={"Permissions"},
     *     summary="List all permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of permissions."
     *     )
     * )
     */    
    public function index()
    {
        try {
            $permissions = PermissionResource::collection(Permission::paginate());
            return ApiService::response($permissions);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.error_retrieving_permissions')], 500);
        }
    }  
    /**
     * @OA\Post(
     *     path="/api/v1/permissions",
     *     tags={"Permissions"},
     *     summary="Create a new permission",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name", "code", "permission_category_id"},
     *             @OA\Property(property="name", type="string", example="edit posts"),
     *             @OA\Property(property="code", type="string", example="EDIT_POSTS"),
     *             @OA\Property(property="permission_category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission created successfully."
     *     )
     * )
     */

     public function store(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255|unique:permissions',
             'code' => 'required|string|max:255|unique:permissions',
             'permission_category_id' => 'required|exists:permission_categories,id',
         ]);
 
         if ($validator->fails()) {
             return ApiService::response($validator->errors(), 422);
         }
 
         try {
             $permission = Permission::create($request->all());
             return new PermissionResource($permission);
         } catch (\Exception $e) {
             return ApiService::response(['error' => __('messages.permission_creation_failed')], 500);
         }
     }

    /**
     * @OA\Get(
     *     path="/api/v1/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Show a permission by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         description="Permission ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission details."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return new PermissionResource($permission);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_not_found')], 404);
        }
    }    
    /**
     * @OA\Put(
     *     path="/api/v1/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Update a permission by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         description="Permission ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="updated edit posts"),
     *             @OA\Property(property="code", type="string", example="UPDATED_EDIT_POSTS"),
     *             @OA\Property(property="permission_category_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated successfully."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|unique:permissions,name,' . $id,
                'code' => 'sometimes|string|max:255|unique:permissions,code,' . $id,
                'permission_category_id' => 'sometimes|exists:permission_categories,id',
            ]);

            if ($validator->fails()) {
                return ApiService::response($validator->errors(), 422);
            }

            $permission->update($request->all());
            return new PermissionResource($permission);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_update_failed')], 500);
        }
    }
    
    /**
     * @OA\Delete(
     *     path="/api/v1/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Delete a permission by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         description="Permission ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission deleted successfully."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();
            return ApiService::response(['message' => __('messages.permission_deleted_success')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_deletion_failed')], 500);
        }
    }
    


    public function roles($permissionId)
    {
        try {
            $permission = Permission::with('roles')->findOrFail($permissionId);
            return RoleResource::collection($permission->roles);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_not_found')], 404);
        }
    }
    
     
}
