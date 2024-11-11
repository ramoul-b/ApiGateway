<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'surname', 
    ];

    protected $dates = ['deleted_at'];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRoleNames() {
        return $this->roles()->pluck('name');
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }
    

    public function roles() {
        return $this->belongsToMany(Role::class, 'accounts', 'user_id', 'role_id');
    }

    public function accountUsingRole() {
        return $this->accountUsing()->with('role');
    }

    public function accountDefault() {
        return $this->accounts()->where('default', 1);
    }

    public function accountUsing() {
        return $this->accounts()->where('using', 1);
    }

    
    /* CHECKKKKK
    public function abilities() {
        //of accountUsing
        return $this->accountUsing()->with('abilities')->get()->pluck('abilities')->flatten();
    }
    */
   
}
