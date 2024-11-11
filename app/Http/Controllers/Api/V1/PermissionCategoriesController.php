<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\PermissionCategory; 
use App\Http\Resources\PermissionCategoryResource;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Permission Categories",
 *     description="API Endpoints of Permission Categories"
 * )
 */
class PermissionCategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/permission-categories",
     *     tags={"Permission Categories"},
     *     summary="List all permission categories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of permission categories."
     *     )
     * )
     */
    public function index()
    {
        try {
            $categories = PermissionCategoryResource::collection(PermissionCategory::paginate());
            return ApiService::response($categories);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.error_retrieving_permission_categories')], 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/v1/permission-categories",
     *     tags={"Permission Categories"},
     *     summary="Create a new permission category",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="General Permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission category created successfully."
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }

        try {
            $category = PermissionCategory::create($request->only(['name']));
            return ApiService::response(new PermissionCategoryResource($category), 201);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_category_creation_failed')], 500);
        }
    }
    
    /**
     * @OA\Put(
     *     path="/api/v1/permission-categories/{categoryId}",
     *     tags={"Permission Categories"},
     *     summary="Update a permission category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Permission Category ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Category Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission category updated successfully."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }

        try {
            $category = PermissionCategory::findOrFail($id);
            $category->update($request->all());
            return ApiService::response(new PermissionCategoryResource($category), 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_category_update_failed')], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/permission-categories/{categoryId}",
     *     tags={"Permission Categories"},
     *     summary="Delete a permission category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Permission Category ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission category deleted successfully."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $category = PermissionCategory::findOrFail($id);
            $category->delete();
            return ApiService::response(['message' => __('messages.permission_category_deleted_success')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => __('messages.permission_category_deletion_failed')], 500);
        }
    }
}
