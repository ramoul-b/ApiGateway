<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\AccountOrganization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;


class AccountController extends Controller
{
    /**
 * @OA\Put(
 *     path="/api/v1/accounts/{accountId}",
 *     tags={"Accounts"},
 *     summary="Update an account",
 *     operationId="updateAccount",
 *     @OA\Parameter(
 *         name="accountId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="surname", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="anagrafica_id", type="integer", example="1"),
 *             @OA\Property(property="anagrafica_address_id", type="integer", example="1")
 *         ) 
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Compte mis à jour avec succès."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account not found"
 *     )
 * )
 */

 public function updateAccount(Request $request, $accountId)
 {
     try {
         $validator = Validator::make($request->all(), [
             'name' => 'sometimes|string|max:255',
             'surname' => 'sometimes|string|max:255',
             'email' => 'sometimes|string|email|max:255|unique:users,email,'.$accountId,
             'username' => 'sometimes|string|max:255|unique:users,username,'.$accountId,
             'password' => 'sometimes|string|min:8',
             'anagrafica_id' => 'nullable|integer', 
             'anagrafica_address_id' => 'nullable|integer',
         ]);

         if ($validator->fails()) {
             return ApiService::response($validator->errors(), 422);
         }

         $account = User::findOrFail($accountId);
         if (!$account) {
             return ApiService::response(['error' => __('messages.account_not_found')], 404);
         }
         $account->update($request->only(['name', 'surname', 'email', 'username']));
         if ($request->filled('password')) {
             $account->password = Hash::make($request->password);
             $account->save();
         }

         return ApiService::response(new UserResource($account), 200);
     } catch (\Exception $e) {
         return ApiService::response(['error' => __('messages.account_update_failed')], 500);
     }
 }

    /**
 * @OA\Delete(
 *     path="/api/v1/accounts/{accountId}",
 *     tags={"Accounts"},
 *     summary="Delete an account",
 *     operationId="deleteAccount",
 * security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="accountId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Compte supprimé avec succès.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account not found"
 *     )
 * )
 */

 public function deleteAccount($accountId)
 {
     try {
         $account = User::find($accountId);

         if (!$account) {
             return response()->json(['error' => __('messages.account_not_found')], 404);
         }

         $account->delete();
         return response()->json(['message' => __('messages.account_deleted_success')]);
     } catch (\Exception $e) {
         return response()->json(['error' => __('messages.account_deletion_failed')], 500);
     }
 }
/**
 * @OA\Get(
 *     path="/api/v1/me",
 *     tags={"Accounts"},
 *     summary="Get the authenticated user's information",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user information",
 *         @OA\JsonContent(
 *             type="object",
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
public function getAuthenticatedUser()
{
    try {
        $user = auth()->user(); 
        return ApiService::response(new UserResource($user->load('roles.permissions', 'accounts.role.permissions')));
    } catch (\Exception $e) {
        // Enregistrement de l'erreur dans les logs pour le débogage
        Log::error('Erreur dans la méthode getAuthenticatedUser : ' . $e->getMessage());
        // Retourner une réponse appropriée ou une exception selon votre cas
        return response()->json(['message' => 'Une erreur est survenue. Veuillez contacter l\'administrateur.'], 500);
    }
}

    /**
 * @OA\Post(
 *     path="/api/v1/accounts/{accountId}/roles",
 *     tags={"Accounts"},
 *     summary="Assign a role to an account",
 *     operationId="assignRoleToAccount",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="accountId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="role_id", type="integer", description="ID of the role to assign")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role assigned to account successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Rôle assigné avec succès au compte.")
 *         )
 *     )
 * )
 */

 public function assignRoleToAccount(Request $request, $accountId)
 {
     try {
         $validator = Validator::make($request->all(), [
             'role_id' => 'required|exists:roles,id',
         ]);

         if ($validator->fails()) {
             return ApiService::response($validator->errors(), 422);
         }

         $account = User::findOrFail($accountId);
         $account->roles()->syncWithoutDetaching([$request->role_id]);

         return ApiService::response(['message' => __('messages.role_assigned_successfully')]);
     } catch (\Exception $e) {
         return response()->json(['error' => __('messages.operation_failed')], 500);
     }
 }
 


 

    // Définir le rôle par défaut d'un compte
    public function setDefaultAccountRole(Request $request, $accountId)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
            ]);

            Account::where('user_id', $accountId)->update(['default' => false]);
            Account::where('user_id', $accountId)->where('role_id', $request->role_id)->update(['default' => true]);

            return response()->json(['message' => __('messages.default_role_set_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.operation_failed')], 500);
        }
    }






    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);
            $user = Auth::user();
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => __('messages.current_password_not_matched')], 422);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return ApiService::response(['message' => __('messages.password_changed_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.operation_failed')], 500);
        }
    }

/**
 * @OA\Put(
 *      path="/api/v1/accounts/{accountId}/switch",
 *      operationId="switchAccount",
 *      tags={"Accounts"},
 *      summary="Switch the user's active account",
 *      description="Switches the currently authenticated user's active account based on the provided account ID.",
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="accountId",
 *          description="ID of the account to switch to",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  description="Success message",
 *                  example="Account switched successfully"
 *              ),
 *              @OA\Property(
 *                  property="account",
 *                  type="object",
 *                  description="The switched account",
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Account not found",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  description="Error message",
 *                  example="Account not found"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal server error",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  description="Error message",
 *                  example="Operation failed"
 *              )
 *          )
 *      )
 * )
 */
    public function switchAccount(Request $request, $accountId)
    {
        try {
            $user = auth()->user();

            $account = Account::where('id', $accountId)
                ->where('user_id', $user->id)
                ->first();

            if (!$account) {
                return ApiService::response(['error' => __('messages.account_not_found')], 404);
            }
            Log::info('Account avant la mise à jour : ' . $account);
            // Désactiver tous les autres comptes
            Account::where('user_id', $user->id)
                ->update(['using' => 0]);
                Log::info('Tous les comptes désactivés.');
            // Activer le compte spécifié
            $account->update(['using' => 1]);
            Log::info('Tous les comptes désactivés.');
            // Recharger l'account mis à jour
            $updatedAccount = Account::find($accountId);
            Log::info('Account rechargé : ' . $updatedAccount);
            return ApiService::response(['message' => __('messages.account_switched_successfully'), 'account' => $updatedAccount]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.operation_failed')], 500);
        }
    }

}
