<?php

use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\RolesController;
use App\Http\Controllers\Api\V1\PermissionsController;
use App\Http\Controllers\Api\V1\PermissionCategoriesController;
use App\Http\Controllers\Api\V1\ApisController;
use App\Http\Controllers\Api\V1\MicroservicesController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\APIGatewayController;
use App\Http\Controllers\Api\V1\RoleRequestController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\LanguageFileController;


//Route::any('/{any}', [APIGatewayController::class, 'handle'])->where('any', '.*');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth:api']);
    // languages  management
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::post('/languages', [LanguageController::class, 'store']);
    Route::put('/languages/{id}', [LanguageController::class, 'update']);
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);
    Route::get('/languages/{iso_639_code}/{type}/content', [LanguageController::class, 'getContent']);
    Route::get('/languages/{iso_639_code}/{type}/content/check/{md5}', [LanguageController::class, 'checkForNewContent']);
    
    // languages  management
    Route::get('/languages-files/{languageId}/files', [LanguageFileController::class, 'index']);
    Route::post('/languages-files/{languageId}/files', [LanguageFileController::class, 'store']);

// Protected routes that require authentication
Route::group(['middleware' => ['auth:api', 'check.api.permission']], function () {
    // Account management
    Route::put('/accounts/{accountId}', [AccountController::class, 'updateAccount'])->middleware(['auth:api']);
    Route::delete('/accounts/{accountId}', [AccountController::class, 'deleteAccount'])->middleware(['auth:api']);
    Route::get('/me', [AccountController::class, 'getAuthenticatedUser'])->middleware(['auth:api']);
    Route::post('/accounts/{accountId}/roles', [AccountController::class, 'assignRoleToAccount'])->middleware(['auth:api']);
    Route::delete('/accounts/{accountId}/roles/{roleId}', [AccountController::class, 'removeRoleFromAccount'])->middleware(['auth:api']);
    Route::post('/accounts/{accountId}/set-default-role', [AccountController::class, 'setDefaultAccountRole'])->middleware(['auth:api']);
    Route::post('/accounts/{accountId}/associate-organization', [AccountController::class, 'associateAccountWithOrganization'])->middleware(['auth:api']);
    Route::post('/accounts/{accountId}/change-default-organization', [AccountController::class, 'changeDefaultOrganization'])->middleware(['auth:api']);
    Route::get('/accounts/{accountId}', [AccountController::class, 'getAccountDetails'])->middleware(['auth:api']);
    Route::post('/accounts/change-password', [AccountController::class, 'changePassword']);
    Route::put('/accounts/{accountId}/switch', [AccountController::class, 'switchAccount']);


    // Role management
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{id}', [RolesController::class, 'show']);
    Route::put('/roles/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);
    Route::get('/roles/{roleId}/permissions', [RolesController::class, 'permissions']);
    Route::post('/roles/{roleId}/permissions/{permissionId}', [RolesController::class, 'attachPermission']);
    Route::delete('/roles/{roleId}/permissions/{permissionId}', [RolesController::class, 'detachPermission']);

    // RoleRequest management
    Route::get('/role-requests', [RoleRequestController::class, 'index']);
    Route::post('/role-requests', [RoleRequestController::class, 'store']);
    Route::get('/role-requests/{id}', [RoleRequestController::class, 'show']);
    Route::put('/role-requests/{id}', [RoleRequestController::class, 'update']);
    Route::delete('/role-requests/{id}', [RoleRequestController::class, 'destroy']);
    Route::post('/roles/request/{roleCode}', [RoleRequestController::class, 'createRequest']);
    Route::get('/roles/request/{roleRequestId}/approve', [RoleRequestController::class, 'approveRequest']);
    Route::get('/roles/request/{roleRequestId}/deny', [RoleRequestController::class, 'denyRequest']);

    // Account specific operations
    Route::post('/accounts/{accountId}/assign-role', [AccountController::class, 'assignRoleToAccount']);
    Route::delete('/accounts/{accountId}/remove-role/{roleId}', [AccountController::class, 'removeRoleFromAccount']);
    Route::post('/accounts/{accountId}/set-default-role', [AccountController::class, 'setDefaultAccountRole']);
    Route::post('/accounts/{accountId}/associate-organization', [AccountController::class, 'associateAccountWithOrganization']);
    Route::post('/accounts/{accountId}/change-default-organization', [AccountController::class, 'changeDefaultOrganization']);
    Route::get('/accounts/{accountId}', [AccountController::class, 'getAccountDetails']);
    Route::post('/accounts/change-password', [AccountController::class, 'changePassword']);

    // Permissions management
    Route::get('/permissions', [PermissionsController::class, 'index']);
    Route::post('/permissions', [PermissionsController::class, 'store']);
    Route::get('/permissions/{id}', [PermissionsController::class, 'show']);
    Route::put('/permissions/{id}', [PermissionsController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionsController::class, 'destroy']);
    Route::get('/permissions/{permissionId}/roles', [PermissionsController::class, 'roles']);
    

    //Route::post('/languages-file',[LanguageFileController::class, 'store']);
    Route::get('/languages-files/{languageId}/files/{fileId}',[LanguageFileController::class, 'show']);
    Route::post('/languages-files/{languageId}/files/{fileId}',[LanguageFileController::class, 'update']);
    Route::delete('/languages-files/{languageId}/files/{fileId}', [LanguageFileController::class, 'destroy']);
    
    // Permission Categories management
    Route::get('/permission-categories', [PermissionCategoriesController::class, 'index']);
    Route::post('/permission-categories', [PermissionCategoriesController::class, 'store']);
    Route::put('/permission-categories/{category}', [PermissionCategoriesController::class, 'update']);
    Route::delete('/permission-categories/{category}', [PermissionCategoriesController::class, 'destroy']);

    // APIs management
    Route::get('/apis', [ApisController::class, 'index']);
    Route::post('/apis', [ApisController::class, 'store']);
    Route::get('/apis/{id}', [ApisController::class, 'show']);
    Route::put('/apis/{id}', [ApisController::class, 'update']);
    Route::delete('/apis/{id}', [ApisController::class, 'destroy']);
  
    // ApiConditions management
    Route::post('/apiconditions/{api_id}', [ApisController::class, 'storeApiConditions']);
    Route::get('/apiconditions/{api_id}', [ApisController::class, 'getApiConditions']);
    Route::put('/apiconditions/{api_id}', [ApisController::class, 'updateApiConditions']);

    // Microservices management
    Route::get('/microservices', [MicroservicesController::class, 'index']);
    Route::post('/microservices', [MicroservicesController::class, 'store']);
    Route::get('/microservices/{id}', [MicroservicesController::class, 'show']);
    Route::put('/microservices/{id}', [MicroservicesController::class, 'update']);
    Route::delete('/microservices/{id}', [MicroservicesController::class, 'destroy']);

    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::delete('/api/v1/users/{id}/roles', [UserController::class, 'updateRoles']);




});
