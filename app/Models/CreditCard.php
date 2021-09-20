<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends ApiModel
{
    use SoftDeletes;

    protected $table = 'maliin.credit_cards';
    protected array $rules = [
        'name'          => 'required',
        'due_day'       => 'required|integer',
        'close_day'     => 'required|integer'
    ];
    protected $dates = [
        'deleted_at'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'due_day',
        'close_day'

    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'due_day' => 'integer',
        'close_day' => 'integer'
    ];

    public function bills(){
        return $this->hasMany('App\Models\Bill');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Invoice');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account');
    }
}
