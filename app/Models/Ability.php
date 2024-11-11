<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ability extends Model
{
    use SoftDeletes;

    protected $fillable = ['role_id', 'permission_id'];

    protected $dates = ['deleted_at'];
    
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function permission() {
        return $this->belongsTo(Permission::class);
    }
}
