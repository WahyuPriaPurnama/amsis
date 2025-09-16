<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'method',
        'ip',
        'agent',
        'role',
        'user_id',
        'username',
        'action',
        'extra',
    ];
}
