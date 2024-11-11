<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\V1\RoleScope;
use App\Observers\V1\RoleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//#[ObservedBy(RoleObserver::class)]
class Role extends Model
{
    use SoftDeletes;
    use RoleScope;
    use HasFactory;

    protected $fillable = ['name', 'code', 'requestable', 'organization_id', 'organization_address_id'];

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    public function accounts() {
        return $this->belongsToMany(Account::class);
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'abilities', 'role_id', 'permission_id');
    }
}
