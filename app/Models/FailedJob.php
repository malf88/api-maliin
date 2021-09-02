<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $table = 'maliin.failed_jobs';
    protected $dates = ['failed_at'];

    protected $visible = [
        'connection',
        'queue',
        'payload',
        'exception',
        'failed_at'
    ];
    protected $casts = [
        'failed_at' => 'timestamp'

    ];
}
