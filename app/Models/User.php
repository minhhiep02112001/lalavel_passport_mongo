<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $collection = 'users';
    protected $table = 'users';
    protected $primaryKey = '_id';
    protected $fillable= ['_id', 'password', 'is_cache', 'password_cache'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_cache'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function findForPassport($username)
    {
        return $this->where('_id',(int) $username)->first();
    }

    public function getAuthUsername() {
        return $this->_id;
    }

    public function getAuthPassword()
    {
        if ($this->is_cache) {
            return $this->password;
        } else {
            return $this->password_cache;
        }
    }

}
