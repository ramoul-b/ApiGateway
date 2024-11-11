<?php

namespace App\Scopes\V1;


trait RoleScope
{
    public function scopeRoleIndex($query)
    {//get by roleIndex on model class
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return $query->where('id', 0);
        $abilities = $account->abilities;
        //if account using has AG_get_role_only_organization permission view only role with organization_id same as account using role
        //elseif account using has AG_get_role_only_organization_address permission view only role with organization_address_id same as account using role
        //else if account using has AG_get_role permission view all roles
        //else view no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role');
        })) {
            return $query;
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role_only_organization');
        })) {
            return $query->where('organization_id', $account->role->organization_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role_only_organization_address');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('organization_address_id', $account->role->organization_address_id);
        }
        else {
            return $query->where('id', 0);
        }
    }

    public function scopeRoleShow($query, $role_id)
    {//get by roleShow on model class
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return $query->where('id', 0);
        $abilities = $account->abilities;
        //if account using has AG_get_role_only_organization permission view only role with organization_id same as account using role
        //elseif account using has AG_get_role_only_organization_address permission view only role with organization_address_id same as account using role
        //else if account using has AG_get_role permission view all roles
        //else view no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role');
        })) {
            return $query->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role_only_organization');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_get_role_only_organization_address');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('organization_address_id', $account->role->organization_address_id)
                ->where('id', $role_id);
        }
        else {
            return $query->where('id', 0);
        }
    }

    public function scopeRoleUpdate($query, $role_id)
    {//get by roleUpdate on model class
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return $query->where('id', 0);
        $abilities = $account->abilities;
        //if account using has AG_edit_role_only_organization permission edit only role with organization_id same as account using role
        //elseif account using has AG_edit_role_only_organization_address permission edit only role with organization_address_id same as account using role
        //else if account using has AG_edit_role permission edit all roles
        //else edit no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role');
        })) {
            return $query->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role_only_organization');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_edit_role_only_organization_address');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('organization_address_id', $account->role->organization_address_id)
                ->where('id', $role_id);
        }
        else {
            return $query->where('id', 0);
        }
    }

    public function scopeRoleDelete($query,$role_id) {
        //get by roleDelete on model class
        $account = optional(auth()->user()->accountUsing)->first();
        if ($account == null) return $query->where('id', 0);
        $abilities = $account->abilities;
        //if account using has AG_delete_role_only_organization permission delete only role with organization_id same as account using role
        //elseif account using has AG_delete_role_only_organization_address permission delete only role with organization_address_id same as account using role
        //else if account using has AG_delete_role permission delete all roles
        //else delete no role
        if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_delete_role');
        })) {
            return $query->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_delete_role_only_organization');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('id', $role_id);
        }
        else if ($abilities->contains(function ($value,$key) {
            return ($value->code == 'AG_delete_role_only_organization_address');
        })) {
            return $query
                ->where('organization_id', $account->role->organization_id)
                ->where('organization_address_id', $account->role->organization_address_id)
                ->where('id', $role_id);
        }
        else {
            return $query->where('id', 0);
        }
    }
    
}