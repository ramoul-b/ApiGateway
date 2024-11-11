<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = ['name', 'code', 'permission_category_id']; 

    protected $dates = ['deleted_at'];

    public function roles() {
        return $this->belongsToMany(Role::class, 'abilities', 'permission_id', 'role_id');
    }
    
    public function category() {
        return $this->belongsTo(PermissionCategory::class, 'permission_category_id');
    }

    public function apis()
    {
        return $this->belongsToMany(Api::class, 'api_conditions', 'permission_id', 'api_id')
            ->withPivot(['condition', 'condition_object', 'condition_level', 'level', 'position_level']);
    }
}
