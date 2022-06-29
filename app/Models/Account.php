<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Account extends ApiModel
{
    use SoftDeletes;

    protected $table = 'maliin.accounts';
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'bank',
        'agency',
        'account',
        'user_id'
    ];

    protected $visible = [
        'id',
        'updated_at',
        'deleted_at',
        'created_at',
        'name',
        'bank',
        'agency',
        'account',
        'user_id',
        'user'
    ];
    protected array $rules = [
        'name'      => 'required|max:100',
        'bank'      => 'required',
        'agency'    => 'required',
        'account'   => 'required'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
    ];

    public function bills(){
        return $this->hasMany('App\Models\Bill');
    }

    public function creditCards(){
        return $this->hasMany('App\Models\CreditCard');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'maliin.accounts_users');
    }


}
