<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'cloud_id',
        'subsidiary_id',
        'pin',
        'scan_date',
        'verify',
        'status_scan',
        'photo_url',
    ];

    /**
     * Relasi ke Master Karyawan Absensi (Berdasarkan PIN)
     */
    public function attendanceEmployee()
    {
        return $this->belongsTo(AttendanceEmployee::class, 'pin', 'pin');
    }

    /**
     * Relasi ke Perusahaan / Subsidiary / Plant
     */
    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiary_id');
    }
}