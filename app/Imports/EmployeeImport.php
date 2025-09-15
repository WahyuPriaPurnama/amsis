<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EmployeeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $role = Auth::user()?->roles->pluck('name')->first();
        $subsidiary_id = match ($role) {
            'super-admin', 'holding-admin' => null, // pakai dari file
            'eln-admin' => 2,
            'eln2-admin' => 3,
            'bofi-admin' => 4,
            'haka-admin' => 5,
            'rmm-admin' => 6,
            default => null,
        };

        // dd($rows);
        foreach ($rows as $row) {
            $nip = $row['nip'];
            $nik = isset($row['nik']) ? ltrim($row['nik'], "'") : null;
            $npwp = isset($row['npwp']) ? ltrim($row['npwp'], "'") : null;
            // Skip jika nip dan nik sudah ada
            $exists = Employee::where('nip', $nip)->exists();
            if ($exists) {
                info("⏭️ Skipped import: NIP {$nip} sudah ada di database.");

                continue;
            }

            $employee = Employee::create([
                'nip' => $nip,
                'nama' => Str::title($row['nama']),
                'nik' => $nik,
                'divisi' => $row['divisi'],
                'departemen' => $row['departement'],
                'seksi' => $row['seksi'],
                'posisi' => $row['posisi'],
                'status_peg' => $row['status_pegawai'],
                'tgl_masuk' => $row['tanggal_masuk'],
                'awal_kontrak' => $row['awal_kontrak'],
                'akhir_kontrak' => $row['akhir_kontrak'],
                'tmpt_lahir' => $row['tempat_lahir'],
                'tgl_lahir' => $row['tanggal_lahir'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'alamat' => $row['alamat'],
                'no_telp' => $row['no_telp'],
                'email' => $row['email'],
                'pend_trkhr' => $row['pendidikan_terakhir'],
                'jurusan' => $row['jurusan'],
                'thn_lulus' => $row['tahun_lulus'],
                'nama_ibu' => $row['nama_ibu'],
                'npwp' => $npwp,
                'status' => $row['status_perkawinan'],
                'jml_ank' => $row['jumlah_anak'],
                'nama_kd' => $row['nama_kontak_darurat'],
                'no_kd' => $row['nomor_kontak_darurat'],
                'hubungan' => $row['hubungan'],
                'subsidiary_id' => $subsidiary_id ?? $row['plant'],
            ]);
            // Cek apakah user sudah ada
            $userExists = \App\Models\User::where('employee_id', $employee->id)->exists();
            if (!$userExists) {
                $baseEmail = $row['email'] ?? strtolower(Str::slug($employee->nama, '.'));
                $uniqueEmail = $this->generateUniqueEmail($baseEmail);

                $user = \App\Models\User::create([
                    'name' => Str::title($employee->nama),
                    'email' => $uniqueEmail,
                    'password' => bcrypt('Karyawan_2025'),
                    'employee_id' => $employee->id,
                ]);

                $user->assignRole('employee');
                info("Imported employee: {$employee->nama} with username{$uniqueEmail}");
            }
        }
    }
    private function generateUniqueEmail(string $base): string
    {
        $email = $base;
        $counter = 1;

        while (\App\Models\User::where('email', $email)->exists()) {
            $email = $base . $counter;
            $counter++;
        }

        return $email;
    }
}
