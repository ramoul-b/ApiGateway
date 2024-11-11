<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Services\ApiService;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Ability;
use App\Scopes\RoleScope as ScopesRoleScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     name="Authorization",
 *     description="Enter JWT Bearer token **_only_**"
 * )
 */
class RolesController extends Controller
{
    private $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }


    /**
     * List all roles.
     * 
     * @OA\Get(
     *     path="/api/v1/roles",
     *     summary="List all roles",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of roles."
     *     )
     * )
     */
    public function index()
    {
        //STEP 1 POLICY
        $this->authorize('index',Role::class);
        Log::info('Authorization passed for index method.');
        //STEP 2 SCOPE
        $roles = Role::roleIndex();
        Log::info('Roles loaded from Role::roleIndex.');
        
        $roles = RoleResource::collection($roles->with('permissions')->get());
        return ApiService::response($roles);
    }
    /**
     * Create a new role.
     * 
     * @OA\Post(
     *     path="/api/v1/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="code", type="string", example="ADMIN_CODE"),
     *             @OA\Property(property="requestable", type="boolean", example="0"),
     *             @OA\Property(property="organization_id", type="integer", example="0"),
     *             @OA\Property(property="organization_address_id", type="integer", example="0"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role created."
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'code' => 'required|string|max:255|unique:roles',
            'requestable' => 'required|boolean',
            'organization_id' => 'required|integer', 
            'organization_address_id' => 'required|integer',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
    
        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }
    
        $this->authorize('store', Role::class);
    
        try {
            $role = Role::create($validator->validated());
            if (!$role->exists) {
                return ApiService::response(['error' => __('messages.role_creation_failed_by_authorization')], 500);
            }
            $role->permissions()->sync($request->get('permissions', []));
            $role = $role->load('permissions');
            return ApiService::response(new RoleResource($role), 201);  // Assurez-vous de retourner 201 ici
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.role_creation_failed')], 500);
        }
    }
        
    

    /**
     * Show a role by ID.
     * 
     * @OA\Get(
     *     path="/api/v1/roles/{id}",
     *     summary="Show a role by ID",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details."
     *     )
     * )
     */


     public function show($id)
     {
        //STEP 1 POLICY
        $this->authorize('show',Role::class);
        //STEP 2 SCOPE
        $roles = Role::roleShow($id);
        
        return new RoleResource($roles->with('permissions')->findOrFail($id));
     }
         
    
    /**
     * Update a role by ID.
     * 
     * @OA\Put(
     *     path="/api/v1/roles/{id}",
     *     summary="Update a role by ID",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="new_role_name"),
     *             @OA\Property(property="code", type="string", example="ADMIN_CODE"),
     *             @OA\Property(property="requestable", type="boolean", example="0"),
     *             @OA\Property(property="organization_id", type="integer", example="0"),
     *             @OA\Property(property="organization_address_id", type="integer", example="0"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *            )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        //STEP 1 ROLE POLICY
        $this->authorize('update',Role::class);
        //STEP 2 SCOPE
        $role = Role::roleUpdate($id)->first();
        //STEP 3 OBSERVER UPDATING (check with !$MODEL->wasChanged())

        if (!$role) {
            return ApiService::response(['error' => __('messages.role_not_found')], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $id,
            'code' => 'sometimes|string|max:255|unique:roles,code,' . $id,
            'requestable' => 'required|boolean',
            //'organization_id' => 'required|integer|exists:organizations,id', 
            //'organization_address_id' => 'required|integer|exists:organization_addresses,id', 
            'organization_id' => 'required|integer',
            'organization_address_id' => 'required|integer',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }

        try {
            $role->update($validator->validated());
            if (!$role->wasChanged()) {
                //observer updating failed
                return ApiService::response(['error' => __('messages.role_update_failed_by_authorization')], 500);
            }
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
                $role = $role->load('permissions');
            }
            return new RoleResource($role);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.role_update_failed')], 500);
        }
    }
    

    /**
     * Delete a role by ID.
     * 
     * @OA\Delete(
     *     path="/api/v1/roles/{id}",
     *     summary="Delete a role by ID",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted."
     *     )
     * )
     */
    public function destroy($id)
    {
        //STEP 1 POLICY
        $this->authorize('delete',Role::class);
        //STEP 2 SCOPE
        $role = Role::roleDelete($id)->first();

        
        if (!$role) {
            return ApiService::response(['error' => __('messages.role_not_found')], 404);
        }

        try {
            $role->delete();
            return ApiService::response(['message' => __('messages.role_deleted_success')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.role_deletion_failed')], 500);
        }
    }
    

    /**
     * Retrieve permissions associated with the role.
     *
     * @OA\Get(
     *     path="/api/v1/roles/{roleId}/permissions",
     *     summary="Get permissions of a role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="roleId",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions retrieved successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found."
     *     )
     * )
     */
    public function permissions($roleId)
    {
        try {
            // Trouver le rôle par son ID
            $role = Role::with('permissions')->findOrFail($roleId);
    
            // Récupérer les permissions associées au rôle
            $permissions = $role->permissions;
    
            // Retourner la liste des permissions sous forme de ressource
            return PermissionResource::collection($permissions);
        } catch (\Exception $e) {
            // Gérer l'erreur et retourner une réponse appropriée
            \Log::error('Error fetching permissions: ' . $e->getMessage());
            return response()->json(['message' => __('messages.permissions_fetch_failed')], 500);
        }
    }
    /**
     * Attach a permission to a role.
     *
     * @OA\Post(
     *     path="/api/v1/roles/{roleId}/permissions/{permissionId}",
     *     summary="Attach a permission to a role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="roleId",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="permissionId",
     *         in="path",
     *         description="Permission ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission attached successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role or Permission not found."
     *     )
     * )
     */

    
    public function attachPermission($roleId, $permissionId)
    {
        try {
            $role = Role::findOrFail($roleId);
    
            // Vérifier si la relation entre le rôle et la permission existe déjà
            $existingRelation = DB::table('abilities')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();
    
            if ($existingRelation) {
                return ApiService::response(['message' => __('messages.permission_already_attached')], 200);
            }
    
            // Si la relation n'existe pas, l'attacher
            $role->permissions()->attach($permissionId);
    
            return ApiService::response(['message' => __('messages.permission_attached_success')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_attachment_failed')], 500);
        }
    }
    
    /**
     * Detach a permission from a role.
     *
     * @OA\Delete(
     *     path="/api/v1/roles/{roleId}/permissions/{permissionId}",
     *     summary="Detach a permission from a role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="roleId",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="permissionId",
     *         in="path",
     *         description="Permission ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission detached successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role or Permission not found."
     *     )
     * )
     */
    public function detachPermission($roleId, $permissionId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->permissions()->detach($permissionId);
            return ApiService::response(['message' => __('messages.permission_detached_success')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_detachment_failed')], 500);
        }
    }
    
    
}