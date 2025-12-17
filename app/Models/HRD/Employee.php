<?php

namespace App\Models\HRD;
use App\Models\User;
use App\Models\HRD\Subsidiary;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }

    public function scopeIndex($query)
    {
        return $query->with('subsidiary');
    }
    protected static function booted()
    {
        static::deleting(function ($employee) {
            // Delete related user if exists
            if ($employee->user) {
                $employee->user->delete();
            }
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }
}
