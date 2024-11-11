<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiCondition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'api_id', 
        'permission_id',
        'condition', 
        'condition_object', 
        'condition_level', 
        'level', 
        'position_level', 
    ];

    protected $dates = ['deleted_at'];

    public function api() {
        return $this->belongsTo(Api::class);
    }

    public function permission() {
        return $this->belongsTo(Permission::class);
    }
}
