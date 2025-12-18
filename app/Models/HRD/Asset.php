<?php

namespace App\Models\HRD;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
