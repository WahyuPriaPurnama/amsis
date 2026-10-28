<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'pin',
        'name_in_machine',
        'cloud_id',
        'subsidiary_id',
    ];

    /**
     * Accessor untuk nama tampilan (Prioritas ke nama mesin -> PIN)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name_in_machine ?? 'Karyawan (PIN: ' . $this->pin . ')';
    }



    /**
     * Relasi ke Perusahaan / Subsidiary / Plant
     */
    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiary_id');
    }

    /**
     * Relasi ke Log Absensi (Berdasarkan PIN)
     */
    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'pin', 'pin');
    }
}
