<?php

namespace App\Models\HRD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'cloud_id',
        'pin',
        'scan_date',
        'verify',
        'status_scan',
        'photo_url',
    ];
}
