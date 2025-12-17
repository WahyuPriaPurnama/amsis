<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scanlog extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function harian()
    {
        return $this->belongsTo(Harian::class, 'pin', 'pin');
    }
}
