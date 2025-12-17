<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harian extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scanlogs()
    {
        return $this->hasMany(Scanlog::class, 'pin', 'pin');
    }
}
