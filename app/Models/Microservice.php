<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Microservice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 
        'secret_key',
        'main_ipv4',
        'load_balancer_ipv4',
        'main_ipv6',
        'load_balancer_ipv6',
    ];
    
    protected $dates = ['deleted_at'];

    public function apis() {
        return $this->hasMany(Api::class);
    }
}
