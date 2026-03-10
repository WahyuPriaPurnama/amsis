<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'seamer_name',
        'rpm',
        'counter',
        'device_id',
        'location'
    ];

    protected $casts = [
        'rpm' => 'float',
        'counter' => 'integer',
        'created_at' => 'datetime'
    ];
}
