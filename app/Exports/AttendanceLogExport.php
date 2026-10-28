<?php

namespace App\Exports;

use App\Models\HRD\AttendanceLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class AttendanceLogExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected string $search;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(string $search = '', ?string $startDate = null, ?string $endDate = null)
    {
        $this->search    = $search;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    /**
     * Query data yang akan diexport
     */
    public function query()
    {
        return AttendanceLog::with(['attendanceEmployee', 'subsidiary'])
            ->when($this->search, function ($query) {
                $query->where('pin', 'like', '%' . $this->search . '%')
                      ->orWhereHas('attendanceEmployee', function ($q) {
                          $q->where('name_in_machine', 'like', '%' . $this->search . '%');
                      });
            })
            // Filter Range Tanggal (Scan Date)
            ->when($this->startDate, function ($query) {
                $query->whereDate('scan_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('scan_date', '<=', $this->endDate);
            })
            ->latest('scan_date');
    }

    public function headings(): array
    {
        return [
            'ID Log',
            'PIN Karyawan',
            'Nama Karyawan',
            'Subsidiary / Plant',
            'Cloud ID Mesin',
            'Waktu Scan',
            'Metode Verifikasi',
            'Status Scan',
            'URL Foto',
        ];
    }

    public function map($log): array
    {
        $verifyText = match ((int) $log->verify) {
            4       => 'Wajah',
            1, 2    => 'Sidik Jari',
            default => (string) ($log->verify ?? '-'),
        };

        return [
            $log->id,
            $log->pin,
            $log->attendanceEmployee->display_name ?? 'Karyawan Baru',
            $log->subsidiary->name ?? '-',
            $log->cloud_id ?? '-',
            Carbon::parse($log->scan_date)->format('d-m-Y H:i:s'),
            $verifyText,
            $log->status_scan ?? '-',
            $log->photo_url ?? '-',
        ];
    }
}