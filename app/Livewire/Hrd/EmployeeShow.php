<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\Employee;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmployeeShow extends Component
{
    public Employee $employee;

    public function mount($employee)
    {
        $this->employee = $employee instanceof Employee
            ? $employee
            : Employee::with(['subsidiary', 'user'])->findOrFail($employee);
    }

    public function deleteEmployee()
    {
        try {
            // Hapus data karyawan
            $this->employee->delete();

            session()->flash('success', 'Data karyawan berhasil dihapus.');
            return $this->redirectRoute('employees.index', navigate: true);

        } catch (\Throwable $e) {
            // Tangkap semua jenis exception (QueryException, PDOException, dll)
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            // Cek jika error mengandung SQLState 23000 (Foreign Key Constraint)
            if ($errorCode == 23000 || str_contains($errorMessage, '23000') || str_contains($errorMessage, '1451')) {
                session()->flash('error', 'Data tidak dapat dihapus karena masih memiliki riwayat RO dan RFP.');
                return;
            }

            session()->flash('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // 1. Hitung Umur
        $age = $this->employee->tgl_lahir
            ? Carbon::parse($this->employee->tgl_lahir)->age
            : '-';

        // 2. Hitung Masa Kerja
        $masaKerja = 'Belum ada data';
        if ($this->employee->tgl_masuk) {
            $start = Carbon::parse($this->employee->tgl_masuk);
            $diff  = $start->diff(Carbon::now());
            $masaKerja = $start->isoFormat('dddd, D MMMM YYYY') . " / {$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari";
        }

        // 3. Hitung Status & Sisa Kontrak PKWT
        $contractStatus = null;
        if ($this->employee->status_peg === 'PKWT' && $this->employee->akhir_kontrak) {
            $akhir = Carbon::parse($this->employee->akhir_kontrak);
            $daysRemaining = (int) floor(Carbon::now()->diffInDays($akhir, false));

            if ($daysRemaining < 0) {
                $contractStatus = [
                    'is_expired' => true,
                    'text' => 'Kontrak sudah berakhir',
                    'date_formatted' => $akhir->format('d M Y')
                ];
            } else {
                $raw = Carbon::now()->diffInDays($akhir, false);
                $days = floor($raw);
                $hours = floor(($raw - $days) * 24);
                $minutes = floor((($raw - $days) * 24 - $hours) * 60);

                $contractStatus = [
                    'is_expired' => false,
                    'text' => "Sisa {$days} hari {$hours} jam {$minutes} menit",
                    'date_formatted' => $akhir->format('d M Y')
                ];
            }
        }

        return view('hrd.employee.show', [
            'age'            => $age,
            'masaKerja'      => $masaKerja,
            'contractStatus' => $contractStatus,
        ])->title("Biodata " . $this->employee->nama);
    }
}