<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\AttendanceLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 
use Livewire\Component;
use Livewire\WithPagination;

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
  
            $response = Http::withHeaders([
                'Authorization' => 'Bearer K0WFK7TV51AD7934',
                'Content-Type'  => 'application/json',
            ])->post('https://developer.fingerspot.io/api/get_attlog', [
                'trans_id'   => (string) time(),
                'cloud_id'   => 'S129000614',
                'start_date' => '2026-10-13', // atau gunakan: now()->startOfMonth()->toDateString()
                'end_date'   => '2026-10-15', // atau gunakan: now()->toDateString()
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
                        }
                    }
                    session()->flash('message', 'Berhasil menyinkronkan ' . count($logs) . ' data absensi!');
                } else {
                    session()->flash('message', 'Tidak ada data absensi baru ditemukan.');
                }
            } else {
                session()->flash('error', 'Gagal terhubung ke API Fingerspot.');
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
