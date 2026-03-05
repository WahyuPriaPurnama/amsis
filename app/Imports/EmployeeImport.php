<?php

namespace App\Imports;

use App\Models\HRD\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class EmployeeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $role = Auth::user()?->roles->pluck('name')->first();
        $subsidiary_id = match ($role) {
            'super-admin', 'holding-admin' => null,
            'eln-admin' => 2,
            'eln2-admin' => 3,
            'bofi-admin' => 4,
            'haka-admin' => 5,
            'rmm-admin' => 6,
            default => null,
        };

        foreach ($rows as $row) {
            $nip = $row['NIP'] ?? null;
            $nik = isset($row['NIK']) ? ltrim($row['NIK'], "'") : null;
            $npwp = isset($row['NPWP']) ? ltrim($row['NPWP'], "'") : null;

            if ($this->isDuplicate($nip, $nik)) {
                info("⏭️ Skipped import: NIP {$nip} atau NIK {$nik} sudah ada di database.");
                continue;
            }

            $employee = Employee::create([
                'nip' => $nip,
                'nama' => Str::title($row['NAMA']),
                'nik' => $nik,
                'divisi' => $row['DIVISI'],
                'departemen' => $row['DEPARTEMEN'],
                'seksi' => $row['SEKSI'],
                'posisi' => $row['POSISI'],
                'status_peg' => $row['STATUS PEGAWAI'],
                'tgl_masuk' => $row['TANGGAL MASUK'],
                'awal_kontrak' => $row['AWAL KONTRAK'],
                'akhir_kontrak' => $row['AKHIR KONTRAK'],
                'tmpt_lahir' => $row['TEMPAT LAHIR'],
                'tgl_lahir' => $row['TANGGAL LAHIR'],
                'jenis_kelamin' => $row['L/P'],
                'alamat' => $row['ALAMAT'],
                'no_telp' => $row['NO. TELP'],
                'email' => $row['EMAIL'],
                'pend_trkhr' => $row['PENDIDIKAN TERAKHIR'],
                'jurusan' => $row['JURUSAN'],
                'thn_lulus' => $row['TAHUN LULUS'],
                'nama_ibu' => $row['NAMA IBU'],
                'npwp' => $npwp,
                'status' => $row['STATUS PERKAWINAN'],
                'jml_ank' => $row['JUMLAH ANAK'],
                'nama_kd' => $row['NAMA KONTAK DARURAT'],
                'no_kd' => $row['NOMOR KONTAK DARURAT'],
                'hubungan' => $row['HUBUNGAN'],
                'subsidiary_id' => $subsidiary_id ?? $row['PLANT'],
            ]);

            if (!User::where('employee_id', $employee->id)->exists()) {
                $baseEmail = $row['EMAIL'] ?? strtolower(Str::slug($employee->nama, '.'));
                $uniqueEmail = $this->generateUniqueEmail($baseEmail);

                $user = User::create([
                    'name' => Str::title($employee->nama),
                    'email' => $uniqueEmail,
                    'password' => bcrypt('Karyawan_2025'),
                    'employee_id' => $employee->id,
                    'subsidiary_id' => $employee->subsidiary_id,
                ]);

                $user->assignRole('employee');
                info("👤 User dibuat untuk {$employee->nama} | Email: {$uniqueEmail}");
            } else {
                info("⏭️ Skip user: Employee {$employee->nama} sudah memiliki akun.");
            }
        }
    }

    private function isDuplicate(?string $nip, ?string $nik): bool
    {
        return ($nip && Employee::where('nip', $nip)->exists()) ||
            ($nik && Employee::where('nik', $nik)->exists());
    }

    private function generateUniqueEmail(string $base): string
    {
        $email = $base;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base . $counter;
            $counter++;
        }

        return $email;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
