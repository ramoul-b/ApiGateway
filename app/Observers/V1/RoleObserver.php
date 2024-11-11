<?php
namespace App\Observers\V1;

use App\Models\Role;

class RoleObserver
{

    public function creating(Role $role)
    {
        //attention manage overwriting security in observer of role
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        //if account using has AG_get_role_only_organization permission set only role with organization_id same as account using role
        //elseif account using has AG_get_role_only_organization_address permission set only role with organization_address_id same as account using role
        //else if account using has AG_get_role permission set all roles
        //else view no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_create_role');
        })) {
            return true;
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_create_role_only_organization');
        })) {
            return $role->organization_id == $account->role->organization_id;
            //If overwrite organization_id
            //$role->organization_id = $account->role->organization_id;
            //return true;
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_create_role_only_organization_address');
        })) {
            return $role->organization_id == $account->role->organization_id 
                && $role->organization_address_id == $account->role->organization_address_id;
            //If overwrite organization_id and organization_address_id
            //$role->organization_id = $account->role->organization_id;
            //$role->organization_address_id = $account->role->organization_address_id;
            //return true;
        }
        else {
            return false;
        }
    }


    public function updating(Role $role)
    {
        //attention manage overwriting security in observer of role
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return false;
        $abilities = $account->abilities;
        //if account using has AG_get_role_only_organization permission set only role with organization_id same as account using role
        //elseif account using has AG_get_role_only_organization_address permission set only role with organization_address_id same as account using role
        //else if account using has AG_get_role permission set all roles
        //else view no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role');
        })) {
            return true;
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role_only_organization');
        })) {
            return $role->organization_id == $account->role->organization_id;
            //if overwrite organization_id
            //$role->organization_id = $account->role->organization_id;
            //return true;
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role_only_organization_address');
        })) {
            return $role->organization_id == $account->role->organization_id 
                && $role->organization_address_id == $account->role->organization_address_id;
            //if overwrite organization_id and organization_address_id
            //$role->organization_id = $account->role->organization_id;
            //$role->organization_address_id = $account->role->organization_address_id;
            //return true;
        }
        else {
            return false;
        }
    }



    /**
     * Handle the Role "created" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Handle the Role "updated" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        //
    }

    /**
     * Handle the Role "deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}
