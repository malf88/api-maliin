<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'maliin.jobs';
    protected $dates = ['reserved_at','available_at','created_at'];
    protected $fillable = [
        'id',
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at',
        'created_at'
    ];
    protected $visible = [
        'id',
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at',
        'created_at'
    ];
    protected $casts = [
        'reserved_at' => 'timestamp',
        'available_at' => 'timestamp',
        'created_at' => 'timestamp'

    ];

}
