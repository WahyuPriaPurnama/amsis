<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = ['temperature', 'humidity', 'device_id', 'location'];
}
