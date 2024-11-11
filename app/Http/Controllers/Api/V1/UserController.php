<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiService;
use App\Http\Resources\UserResource;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Lang;

class UserController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/v1/users",
 *     tags={"Users"},
 *     summary="List all users with their roles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", description="User ID"),
 *                 @OA\Property(property="name", type="string", description="User name"),
 *                 @OA\Property(property="surname", type="string", description="User surname"),
 *                 @OA\Property(property="email", type="string", format="email", description="User email"),
 *                 @OA\Property(property="username", type="string", description="User username"),
 *                 @OA\Property(
 *                     property="roles",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", description="Role ID"),
 *                         @OA\Property(property="name", type="string", description="Role name"),
 *                         @OA\Property(property="code", type="string", description="Role code")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function index()
    {
        try {
            $users = UserResource::collection(User::with('roles')->paginate());
            return ApiService::response($users);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.error_retrieving_users')], 500);
        }
    }
    
/**
 * @OA\Post(
 *     path="/api/v1/users",
 *     tags={"Users"},
 *     summary="Create a new user with a role",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John"),
 *             @OA\Property(property="surname", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *             @OA\Property(property="username", type="string", example="johndoe"),
 *             @OA\Property(property="anagrafica_id", type="integer", example="1"),
 *             @OA\Property(property="anagrafica_address_id", type="integer", example="1"),
 *             @OA\Property( 
 *                 property="role_ids",
 *                 type="array",
 *                 @OA\Items(type="integer", example=1),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id', 
            'anagrafica_id' => 'nullable|integer', // Valider si nécessaire
            'anagrafica_address_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }
        try {
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        foreach ($request->role_ids as $role_id) {
            $user->accounts()->create([
                'role_id' => $role_id,
                'default' => 1, 
            ]);
        }

         return new UserResource($user);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.user_creation_failed')], 500);
        }     
    }

/**
 * @OA\Get(
 *     path="/api/v1/users/{id}",
 *     tags={"Users"},
 *     summary="Get a specific user by ID with their roles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
    public function show($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            return new UserResource($user);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.user_not_found')], 404);
        }
    }
    
/**
 * @OA\Put(
 *     path="/api/v1/users/{id}",
 *     tags={"Users"},
 *     summary="Update a user's information and their roles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John"),
 *             @OA\Property(property="surname", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *             @OA\Property(property="username", type="string", example="johndoe"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="anagrafica_id", type="integer", example="1"),
 *             @OA\Property(property="anagrafica_address_id", type="integer", example="1"),
 *             @OA\Property(
 *                 property="role_ids",
 *                 type="array",
 *                 @OA\Items(type="integer", example=1),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
public function update(Request $request, $id)
{
    try {
        $user = User::find($id);

        if (!$user) {
            return ApiService::response(['message' => __('messages.user_not_found')], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'surname' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$id,
            'username' => 'sometimes|string|max:255|unique:users,username,'.$id,
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id', 
            'role_ids' => 'sometimes|array',
            'role_ids.*' => 'exists:roles,id',
            'anagrafica_id' => 'nullable|integer', 
            'anagrafica_address_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }
        $user->update($request->except(['password', 'role_id', 'role_ids']));
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        } elseif ($request->has('role_id')) {
            $user->roles()->sync([$request->role_id]);
        }
        \Log::info('User after update:', $user->toArray());
        return new UserResource($user);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.error_updating_user')], 500);
    }
}
 /**
 * @OA\Delete(
 *     path="/api/v1/users/{id}",
 *     tags={"Users"},
 *     summary="Delete a user and their role associations",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
    public function destroy($id)
{
    $user = User::find($id); 

    if (!$user) {
         return ApiService::response(['message' => Lang::get('messages.user_not_found')], 404);
    }

    $user->roles()->detach(); 
    $user->delete();

     return ApiService::response(['message' => Lang::get('messages.user_deleted_success')], 200);
}


}
