<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'microservice_id',
        'route_in',
        'method',
    ];

    protected $dates = ['deleted_at'];

    public function microservice() {
        return $this->belongsTo(Microservice::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'api_conditions', 'api_id', 'permission_id')
            ->withPivot(['condition', 'condition_object', 'condition_level', 'level', 'position_level']);
    }
    
    public function conditions()
    {
        return $this->hasMany(ApiCondition::class);
    }
}
