<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
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
        'user_id'
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

}
