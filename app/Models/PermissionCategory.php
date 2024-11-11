<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionCategory extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function permissions() {
        return $this->hasMany(Permission::class);
    }
}
