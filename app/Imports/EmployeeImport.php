<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmployeeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $user = Auth::user()->role;
        $subsidiary_id = match ($user) {
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
            $nik = ltrim($row['nik'], "'");

            // Skip jika nip dan nik sudah ada
            $exists = Employee::where('nip', $nip)->where('nik', $nik)->exists();
            if ($exists) {
                continue;
            }

            Employee::create([
                'nip' => $nip,
                'nama' => $row['nama'],
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
                'npwp' => ltrim($row['npwp'], "'"),
                'status' => $row['status_perkawinan'],
                'jml_ank' => $row['jumlah_anak'],
                'nama_kd' => $row['nama_kontak_darurat'],
                'no_kd' => $row['nomor_kontak_darurat'],
                'hubungan' => $row['hubungan'],
                'subsidiary_id' => $subsidiary_id ?? $row['plant'],
            ]);
        }
    }
}
