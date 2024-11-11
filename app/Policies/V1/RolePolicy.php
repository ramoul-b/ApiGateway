<?php

namespace App\Policies\V1;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{

    public function index(User $user)
    {
        //attention manage visibility in scopes of roles
        $account = optional($user->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        return $abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role') 
                or ($value->code == 'AG_get_role_only_organization') 
                or ($value->code == 'AG_get_role_only_organization_address');
        });
    }

    public function show(User $user)
    {
        return $this->index($user);
    }

    public function store(User $user)
    {
        //attention manage overwriting security in observer of role
        $account = optional($user->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        return $abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_create_role') 
                or ($value->code == 'AG_create_role_only_organization') 
                or ($value->code == 'AG_create_role_only_organization_address');
        });
    }

    public function update(User $user)
    {
        //attention manage overwriting security in observer of role
        $account = optional($user->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        return $abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role') 
                or ($value->code == 'AG_edit_role_only_organization') 
                or ($value->code == 'AG_edit_role_only_organization_address');
        });
    }

    public function delete(User $user)
    {
        //attention manage overwriting security in observer of role
        $account = optional($user->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        return $abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_delete_role') 
                or ($value->code == 'AG_delete_role_only_organization') 
                or ($value->code == 'AG_delete_role_only_organization_address');
        });
    }
    
}