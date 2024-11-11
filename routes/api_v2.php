<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\RolesController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth:api']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/users', [AuthController::class, 'index'])->middleware('role:admin');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{id}', [RolesController::class, 'show']);
    Route::put('/roles/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);
    Route::get('/roles/{roleId}/permissions', [RolesController::class, 'permissions']);
    Route::post('/roles/{roleId}/permissions/{permissionId}', [RolesController::class, 'attachPermission']);
    Route::delete('/roles/{roleId}/permissions/{permissionId}', [RolesController::class, 'detachPermission']);


    Route::get('/me', [AccountController::class, 'getAuthenticatedUser']);

    Route::put('/accounts/{accountId}', [AccountController::class, 'updateAccount']);
    
    // Supprimer un compte
    Route::delete('/accounts/{accountId}', [AccountController::class, 'deleteAccount']);
    
    // Assigner un rôle à un compte
    Route::post('/accounts/{accountId}/assign-role', [AccountController::class, 'assignRoleToAccount']);
    
    // Retirer un rôle d'un compte
    Route::delete('/accounts/{accountId}/remove-role/{roleId}', [AccountController::class, 'removeRoleFromAccount']);
    
    // Définir le rôle par défaut pour un compte
    Route::post('/accounts/{accountId}/set-default-role', [AccountController::class, 'setDefaultAccountRole']);
    
    // Associer un compte à une organisation
    Route::post('/accounts/{accountId}/associate-organization', [AccountController::class, 'associateAccountWithOrganization']);
    
    // Changer l'organisation par défaut pour un compte
    Route::post('/accounts/{accountId}/change-default-organization', [AccountController::class, 'changeDefaultOrganization']);
    
    // Obtenir les détails d'un compte
    Route::get('/accounts/{accountId}', [AccountController::class, 'getAccountDetails']);
    
    // Changer le mot de passe d'un utilisateur
    Route::post('/accounts/change-password', [AccountController::class, 'changePassword']);
});

