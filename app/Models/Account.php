<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'role_id', 'default', 'anagrafica_id', 'anagrafica_address_id','using'];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function abilities() {
        return $this->belongsToMany(Permission::class, 'abilities', 'role_id', 'permission_id', 'role_id', 'id');
    }

}
