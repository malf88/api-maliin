<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiModel extends Model
{
    protected array $rules = [];
    public static $autoValidates = true;
    protected static function boot()
    {
        parent::boot();
        // or static::creating, or static::updating
        static::saving(function($model)
        {
            if ($model::$autoValidates) {
                return $model->validate();
            }
        });
    }
    public function validate()
    {
        // make a new validator object
        $v = Validator::make($this->attributesToArray(), $this->rules);
        if($v->fails()){
            throw new ValidationException($v);
        }

    }
}
