<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ApiPermission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PermissionService
{

    public function authenticatedCheckPermission($permissionCode) : bool
    {
        //get account using and check permission
        $account = optional(Auth::user()->accountUsing)->first();
        return $this->checkPermission($permissionCode, $account);
    }

    public function checkPermission($permissionCode, \App\Models\Account $account) : bool
    {
        if ($account == null) return false;
        return $account->abilities()->where('code', $permissionCode)->exists();
    }




    // CHeck if delete this code or not

    /*
    public function __construct(Account $account)
    { // ????
        $this->account = $account;
    }
    */

    public function hasPermission(User $user, $apiId)
    {
        $apiPermissions = ApiPermission::where('api_id', $apiId)->pluck('permission_id')->all();
        $userPermissions = $user->permissions()->pluck('id')->all();

        // Check if the user has one of the required permissions for the API
        $commonPermissions = array_intersect($apiPermissions, $userPermissions);

        if ($commonPermissions) {
            return [true, []];
        } else {
            // Retrieve other user accounts that have the permission
            $otherAccounts = $this->getOtherAccountsWithPermission($user, $apiPermissions);
            return [false, $otherAccounts];
        }
    }

    public function check($user, $permissionCode)
    {
        // Vérifiez si l'utilisateur possède une des permissions requises pour l'API.
        $apiPermissions = ApiPermission::whereHas('permission', function ($query) use ($permissionCode) {
            $query->where('code', $permissionCode);
        })->pluck('permission_id')->all();
    
        $userPermissions = $user->permissions()->pluck('id')->all();
        $commonPermissions = array_intersect($apiPermissions, $userPermissions);
    
        if ($commonPermissions) {
            return [true, []];
        } else {
            // Récupérez les autres comptes de l'utilisateur qui ont la permission.
            $otherAccounts = $this->getOtherAccountsWithPermission($user, $permissionCode);
            return [false, $otherAccounts];
        }
    }
    
    protected function getOtherAccountsWithPermission(User $user, array $permissionCodes)
    {
        // Assuming the User model is properly related to Account and Permission models
        return $user->accounts->filter(function ($account) use ($permissionCodes) {
            return $account->permissions->pluck('code')->intersect($permissionCodes)->isNotEmpty();
        });
    }


}
