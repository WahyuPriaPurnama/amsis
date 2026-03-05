<?php

namespace App\Exports;

use App\Models\HRD\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EmployeeExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return ['NIP', 'NAMA', 'NIK', 'DIVISI', 'DEPARTEMEN', 'SEKSI', 'POSISI', 'STATUS PEGAWAI', 'TANGGAL MASUK', 'AWAL KONTRAK', 'AKHIR KONTRAK', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'L/P', 'ALAMAT', 'NO. TELP', 'EMAIL', 'PENDIDIKAN TERAKHIR', 'JURUSAN', 'TAHUN LULUS', 'NAMA IBU', 'NPWP', 'STATUS PERKAWINAN', 'JUMLAH ANAK', 'NAMA KONTAK DARURAT', 'NOMOR KONTAK DARURAT', 'HUBUNGAN', 'PLANT'];
    }

    public function collection()
    {
        $user = Auth::user()->hasRole(['super-admin', 'holding-admin', 'eln-admin', 'eln2-admin', 'bofi-admin', 'haka-admin', 'rmm-admin']);
        $data = Employee::get(['nip', 'nama', 'nik', 'divisi', 'departemen', 'seksi', 'posisi', 'status_peg', 'tgl_masuk', 'awal_kontrak', 'akhir_kontrak', 'tmpt_lahir', 'tgl_lahir', 'jenis_kelamin', 'alamat', 'no_telp', 'email', 'pend_trkhr', 'jurusan', 'thn_lulus', 'nama_ibu', 'npwp', 'status', 'jml_ank', 'nama_kd', 'no_kd', 'hubungan', 'subsidiary_id']);
        if (($user == 'super-admin') or ($user == 'holding-admin')) {
            return $data;
        } elseif ($user == 'eln-admin') {
            return $data->where('subsidiary_id', '2');
        } elseif ($user == 'eln2-admin') {
            return $data->where('subsidiary_id', '3');
        } elseif ($user == 'bofi-admin') {
            return $data->where('subsidiary_id', '4');
        } elseif ($user == 'rmm-admin') {
            return $data->where('subsidiary_id', '6');
        } elseif ($user == 'haka-admin') {
            return $data->where('subsidiary_id', '5');
        }
    }
    public function map($e): array
    {
        return [
            $e->nip,
            $e->nama,
            "'" . $e->nik, // apostrof agar dibaca sebagai teks
            $e->divisi,
            $e->departemen,
            $e->seksi,
            $e->posisi,
            $e->status_peg,
            $e->tgl_masuk,
            $e->awal_kontrak,
            $e->akhir_kontrak,
            $e->tmpt_lahir,
            $e->tgl_lahir,
            $e->jenis_kelamin,
            $e->alamat,
            $e->no_telp,
            $e->email,
            $e->pend_trkhr,
            $e->jurusan,
            $e->thn_lulus,
            $e->nama_ibu,
            "'" . $e->npwp, // aman dari notasi ilmiah
            $e->status,
            $e->jml_ank,
            $e->nama_kd,
            $e->no_kd,
            $e->hubungan,
            $e->subsidiary_id,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT
        ];
    }
}
