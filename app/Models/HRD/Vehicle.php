<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }
    public function scopeIndex($query)
    {
        return $query->with('subsidiary');
    }
}
