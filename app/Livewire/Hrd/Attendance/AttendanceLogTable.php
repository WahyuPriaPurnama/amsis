<?php

namespace App\Livewire\Hrd\Attendance;

use App\Models\HRD\AttendanceEmployee;
use App\Models\HRD\AttendanceLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceLogExport;

class AttendanceLogTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $search = '';
    // Tambahkan properti ini di Class AttendanceLogTable
    public $selectedPhotoUrl = null;

    // Method untuk membuka modal preview foto
    public function previewPhoto($url)
    {
        $this->selectedPhotoUrl = $url;
    }

    // Method untuk menutup modal
    public function closePhotoModal()
    {
        $this->selectedPhotoUrl = null;
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function exportExcel()
    {
        $fileName = 'Data_Absensi_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new AttendanceLogExport($this->search), $fileName);
    }
    public function syncFromFingerspot()
    {
        try {
            $cloudId      = 'S129000614';
            $subsidiaryId = $this->resolveSubsidiaryId($cloudId);

            $totalSynced = 0;
            $totalDays   = 30;
            $stepDays    = 2;

            $overallEndDate   = Carbon::now();
            $overallStartDate = Carbon::now()->subDays($totalDays);

            $currentStartDate = clone $overallStartDate;

            while ($currentStartDate->lt($overallEndDate)) {
                $currentEndDate = (clone $currentStartDate)->addDays($stepDays);

                if ($currentEndDate->gt($overallEndDate)) {
                    $currentEndDate = clone $overallEndDate;
                }

                $startDateFormatted = $currentStartDate->toDateString();
                $endDateFormatted   = $currentEndDate->toDateString();

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer K0WFK7TV51AD7934',
                    'Content-Type'  => 'application/json',
                ])->post('https://developer.fingerspot.io/api/get_attlog', [
                    'trans_id'   => (string) time(),
                    'cloud_id'   => $cloudId,
                    'start_date' => $startDateFormatted,
                    'end_date'   => $endDateFormatted,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $logs = $responseData['data'] ?? [];

                    if (is_array($logs)) {
                        foreach ($logs as $log) {
                            $pin      = $log['pin'] ?? null;
                            $scanDate = $log['scan_date'] ?? null;

                            if ($pin && $scanDate) {
                                // 1. Simpan/Daftarkan PIN ke attendance_employees
                                $attendanceEmployee = AttendanceEmployee::firstOrCreate(
                                    ['pin' => $pin],
                                    [
                                        'cloud_id'      => $cloudId,
                                        'subsidiary_id' => $subsidiaryId,
                                    ]
                                );

                                // Jika nama di mesin belum ada, panggil get_userinfo
                                if (empty($attendanceEmployee->name_in_machine)) {
                                    $this->requestUserInfoFromMachine($cloudId, $pin);
                                }

                                // 2. Simpan Log Absensi beserta subsidiary_id
                                AttendanceLog::updateOrCreate(
                                    [
                                        'pin'       => $pin,
                                        'scan_date' => $scanDate,
                                    ],
                                    [
                                        'cloud_id'      => $cloudId,
                                        'subsidiary_id' => $subsidiaryId,
                                        'verify'        => $log['verify'] ?? null,
                                        'status_scan'   => $log['status_scan'] ?? null,
                                        'photo_url'     => $log['photo_url'] ?? null,
                                    ]
                                );

                                $totalSynced++;
                            }
                        }
                    }
                } else {
                    Log::warning("Gagal narik data Fingerspot untuk periode: {$startDateFormatted} s/d {$endDateFormatted}");
                }

                $currentStartDate->addDays($stepDays);
            }

            if ($totalSynced > 0) {
                session()->flash('message', 'Berhasil menyinkronkan ' . $totalSynced . ' record data absensi dari 30 hari terakhir!');
            } else {
                session()->flash('message', 'Tidak ada data absensi baru ditemukan dalam 30 hari terakhir.');
            }
        } catch (\Exception $e) {
            Log::error('Sync Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Meminta nama karyawan ke mesin Fingerspot via API
     */
    private function requestUserInfoFromMachine($cloudId, $pin)
    {
        try {
            Http::withHeaders([
                'Authorization' => 'Bearer K0WFK7TV51AD7934',
                'Content-Type'  => 'application/json',
            ])->post('https://developer.fingerspot.io/api/get_userinfo', [
                'trans_id' => (string) time() . '_' . $pin,
                'cloud_id' => $cloudId,
                'pin'      => (string) $pin,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed auto get_userinfo for PIN {$pin}: " . $e->getMessage());
        }
    }

    /**
     * Pemetaan Cloud ID ke ID Subsidiary
     */
    private function resolveSubsidiaryId(?string $cloudId): ?int
    {
        return match ($cloudId) {
            'S129000614' => 10, // Jember (ID 10)
            default      => null,
        };
    }

    public function render()
    {
        $logs = AttendanceLog::with('attendanceEmployee')
            ->when($this->search, function ($query) {
                $query->where('pin', 'like', '%' . $this->search . '%')
                    ->orWhereHas('attendanceEmployee', function ($q) {
                        $q->where('name_in_machine', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest('scan_date')
            ->paginate(10);

        return view('livewire.hrd.attendance.attendance-index', compact('logs'));
    }
}
