<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends ApiModel
{
    use SoftDeletes;

    protected $table = 'maliin.categories';
    protected array $rules = [
        'name'              => 'required',
        'is_investiment'    => 'required'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_investiment'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'is_investiment' => 'boolean'
    ];

    public function bills(){
        return $this->hasMany('App\Models\Bill');
    }

}
