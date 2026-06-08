<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetortLog extends Model
{
    protected $table = 'retort_logs';
    protected $fillable = ['temperature', 'retort_id'];
}
