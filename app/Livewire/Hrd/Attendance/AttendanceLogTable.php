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
    public $startDate;
    public $endDate;
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
    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        $fileName = 'Data_Absensi_';

        if ($this->startDate && $this->endDate) {
            $fileName .= $this->startDate . '_sd_' . $this->endDate;
        } else {
            $fileName .= Carbon::now()->format('Y-m-d_His');
        }

        $fileName .= '.xlsx';

        return Excel::download(
            new AttendanceLogExport($this->search, $this->startDate, $this->endDate),
            $fileName
        );
    }

    public function syncFromFingerspot()
    {
        try {
            $cloudId      = 'S129000614';
            $subsidiaryId = $this->resolveSubsidiaryId($cloudId);

            $totalSynced = 0;

            // Jika filter tanggal di UI diisi, tarik sesuai filter. Jika kosong, tarik 30 hari terakhir.
            $overallEndDate   = !empty($this->endDate) ? Carbon::parse($this->endDate) : Carbon::now();
            $overallStartDate = !empty($this->startDate) ? Carbon::parse($this->startDate) : Carbon::now()->subDays(30);

            // Gunakan step 5 hari agar jumlah request HTTP lebih sedikit (6 request untuk 30 hari) & cepat
            $stepDays         = 5;
            $currentStartDate = clone $overallStartDate;

            while ($currentStartDate->lte($overallEndDate)) {
                $currentEndDate = (clone $currentStartDate)->addDays($stepDays);

                if ($currentEndDate->gt($overallEndDate)) {
                    $currentEndDate = clone $overallEndDate;
                }

                $startDateFormatted = $currentStartDate->toDateString();
                $endDateFormatted   = $currentEndDate->toDateString();

                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer K0WFK7TV51AD7934',
                    'Content-Type'  => 'application/json',
                ])->post('https://developer.fingerspot.io/api/get_attlog', [
                    // Wajib unik per request agar tidak diabaikan API Fingerspot
                    'trans_id'   => uniqid('sync_', true),
                    'cloud_id'   => $cloudId,
                    'start_date' => $startDateFormatted,
                    'end_date'   => $endDateFormatted,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();

                    // Log response untuk memastikan data dari API Fingerspot terbaca
                    Log::info("Fingerspot Sync [{$startDateFormatted} s/d {$endDateFormatted}]:", [
                        'count' => is_array($responseData['data'] ?? null) ? count($responseData['data']) : 0
                    ]);

                    $logs = $responseData['data'] ?? [];

                    if (is_array($logs)) {
                        foreach ($logs as $log) {
                            $pin      = $log['pin'] ?? null;
                            $scanDate = $log['scan_date'] ?? null;

                            if ($pin && $scanDate) {
                                $attendanceEmployee = AttendanceEmployee::firstOrCreate(
                                    ['pin' => $pin],
                                    [
                                        'cloud_id'      => $cloudId,
                                        'subsidiary_id' => $subsidiaryId,
                                    ]
                                );

                                if (empty($attendanceEmployee->name_in_machine)) {
                                    $this->requestUserInfoFromMachine($cloudId, $pin);
                                }

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
                    Log::warning("Gagal narik data Fingerspot ({$startDateFormatted} s/d {$endDateFormatted}): " . $response->body());
                }

                // Maju ke hari berikutnya setelah currentEndDate
                $currentStartDate = (clone $currentEndDate)->addDay();
            }

            if ($totalSynced > 0) {
                session()->flash('message', "Berhasil menyinkronkan {$totalSynced} record data absensi!");
            } else {
                session()->flash('message', 'Tidak ada data absensi ditemukan pada periode tersebut.');
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
        $logs = AttendanceLog::with(['attendanceEmployee', 'subsidiary'])
            ->when($this->search, function ($query) {
                $query->where('pin', 'like', '%' . $this->search . '%')
                    ->orWhereHas('attendanceEmployee', function ($q) {
                        $q->where('name_in_machine', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('scan_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('scan_date', '<=', $this->endDate);
            })
            ->latest('scan_date')
            ->paginate(10);

        return view('livewire.hrd.attendance.attendance-index', compact('logs'));
    }
}
