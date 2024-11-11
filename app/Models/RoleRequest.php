<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'role_id',
        'account_id',
        'status',
    ];

    // Define relationships here
    public function role() {
        return $this->belongsTo(Role::class);
    }

  
}
