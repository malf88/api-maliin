<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'maliin.users';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'document',
        'gender',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function accounts(){
        return $this->hasMany('App\Models\Account');
    }

    public function categories(){
        return $this->hasMany('App\Models\Category');
    }
    public function wallets(){
        return $this->hasMany('App\Models\Wallet');
    }

    public function userHasAccount(int $accountId):bool
    {
        return $this->accounts()->find($accountId) != null;
    }

    public function userHasCateogory(int $categoryId):bool
    {
        return $this->categories()->find($categoryId) != null;
    }

}
