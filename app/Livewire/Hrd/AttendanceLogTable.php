<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\AttendanceLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AttendanceLogTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function syncFromFingerspot()
    {
        try {
            $totalSynced = 0;

            // Rentang total: 30 hari ke belakang sampai hari ini
            $totalDays = 30;
            $stepDays = 2; // Batas maksimal API Fingerspot (2 hari per request)

            $overallEndDate = Carbon::now();
            $overallStartDate = Carbon::now()->subDays($totalDays);

            $currentStartDate = clone $overallStartDate;

            // Loop untuk membagi 30 hari menjadi beberapa request berdurasi 2 hari
            while ($currentStartDate->lt($overallEndDate)) {
                $currentEndDate = (clone $currentStartDate)->addDays($stepDays);

                // Pastikan end_date tidak melebihi hari ini
                if ($currentEndDate->gt($overallEndDate)) {
                    $currentEndDate = clone $overallEndDate;
                }

                // Format tanggal menjadi YYYY-MM-DD
                $startDateFormatted = $currentStartDate->toDateString();
                $endDateFormatted   = $currentEndDate->toDateString();

                // Panggil API Fingerspot per rentang 2 hari
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer K0WFK7TV51AD7934',
                    'Content-Type'  => 'application/json',
                ])->post('https://developer.fingerspot.io/api/get_attlog', [
                    'trans_id'   => (string) time(),
                    'cloud_id'   => 'S129000614',
                    'start_date' => $startDateFormatted,
                    'end_date'   => $endDateFormatted,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $logs = $responseData['data'] ?? [];

                    if (is_array($logs)) {
                        foreach ($logs as $log) {
                            if (isset($log['pin']) && isset($log['scan_date'])) {
                                AttendanceLog::updateOrCreate(
                                    [
                                        'pin'       => $log['pin'],
                                        'scan_date' => $log['scan_date'],
                                    ],
                                    [
                                        'cloud_id'    => 'S129000614',
                                        'verify'      => $log['verify'] ?? null,
                                        'status_scan' => $log['status_scan'] ?? null,
                                        'photo_url'   => $log['photo_url'] ?? null,
                                    ]
                                );
                                $totalSynced++;
                            }
                        }
                    }
                } else {
                    Log::warning("Gagal narik data Fingerspot untuk periode: {$startDateFormatted} s/d {$endDateFormatted}");
                }

                // Geser start_date untuk iterasi berikutnya (melompat 2 hari)
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

    public function render()
    {
        $logs = AttendanceLog::query()
            ->when($this->search, function ($query) {
                $query->where('pin', 'like', '%' . $this->search . '%');
            })
            ->latest('scan_date')
            ->paginate(10);

        return view('livewire.hrd.attendance.attendance-index', compact('logs'));
    }
}
