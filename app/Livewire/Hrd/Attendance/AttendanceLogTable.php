<?php

namespace App\Livewire\Hrd\Attendance;

use App\Exports\AttendanceLogExport;
use App\Models\HRD\AttendanceEmployee;
use App\Models\HRD\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceLogTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $startDate;
    public $endDate;
    public $search = '';
    public $selectedPhotoUrl = null;

    public function previewPhoto($url)
    {
        $this->selectedPhotoUrl = $url;
    }

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
        // Perpanjang batas waktu eksekusi PHP untuk penarikan 30 hari
        set_time_limit(120);

        try {
            $cloudId      = 'S129000614';
            $subsidiaryId = $this->resolveSubsidiaryId($cloudId);

            $totalSynced     = 0;
            $pinsMissingName = [];

            // Jika filter tanggal diisi, tarik sesuai filter. Jika kosong, tarik 30 hari ke belakang hingga hari ini.
            $overallEndDate   = !empty($this->endDate) ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now()->endOfDay();
            $overallStartDate = !empty($this->startDate) ? Carbon::parse($this->startDate)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();

            $stepDays         = 5;
            $currentStartDate = clone $overallStartDate;

            while ($currentStartDate->lte($overallEndDate)) {
                $currentEndDate = (clone $currentStartDate)->addDays($stepDays);

                if ($currentEndDate->gt($overallEndDate)) {
                    $currentEndDate = clone $overallEndDate;
                }

                $startDateFormatted = $currentStartDate->format('Y-m-d');
                $endDateFormatted   = $currentEndDate->format('Y-m-d');

                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer K0WFK7TV51AD7934',
                    'Content-Type'  => 'application/json',
                ])->post('https://developer.fingerspot.io/api/get_attlog', [
                    'trans_id'   => uniqid('sync_', true),
                    'cloud_id'   => $cloudId,
                    'start_date' => $startDateFormatted,
                    'end_date'   => $endDateFormatted,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $logs         = $responseData['data'] ?? [];

                    Log::info("Fingerspot Sync [{$startDateFormatted} s/d {$endDateFormatted}]", [
                        'status' => $responseData['success'] ?? null,
                        'count'  => is_array($logs) ? count($logs) : 0,
                    ]);

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

                                // Kumpulkan PIN yang belum ada namanya (hindari spam API di dalam loop)
                                if (empty($attendanceEmployee->name_in_machine)) {
                                    $pinsMissingName[$pin] = true;
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

                $currentStartDate = (clone $currentEndDate)->addDay()->startOfDay();
            }

            // Minta nama ke mesin cukup 1x per PIN yang belum memiliki nama
            foreach (array_keys($pinsMissingName) as $missingPin) {
                $this->requestUserInfoFromMachine($cloudId, $missingPin);
            }

            $this->resetPage();

            if ($totalSynced > 0) {
                session()->flash('message', "Berhasil menyinkronkan {$totalSynced} record data absensi!");
            } else {
                session()->flash('warning', "Tidak ada data absensi ditemukan dari server Fingerspot pada periode {$overallStartDate->format('d-m-Y')} s/d {$overallEndDate->format('d-m-Y')}.");
            }
        } catch (\Throwable $e) {
            Log::error('Sync Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage());
        }
    }

    private function requestUserInfoFromMachine($cloudId, $pin)
    {
        try {
            Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer K0WFK7TV51AD7934',
                'Content-Type'  => 'application/json',
            ])->post('https://developer.fingerspot.io/api/get_userinfo', [
                'trans_id' => uniqid('user_', true),
                'cloud_id' => $cloudId,
                'pin'      => (string) $pin,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed auto get_userinfo for PIN {$pin}: " . $e->getMessage());
        }
    }

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
                // Kelompokkan kondisi OR agar tidak merusak filter startDate & endDate
                $query->where(function ($subQuery) {
                    $subQuery->where('pin', 'like', '%' . $this->search . '%')
                        ->orWhereHas('attendanceEmployee', function ($q) {
                            $q->where('name_in_machine', 'like', '%' . $this->search . '%');
                        });
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
