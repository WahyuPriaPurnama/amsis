<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\Employee;
use App\Models\HRD\Subsidiary;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Input Data Karyawan')]
class EmployeeCreate extends Component
{
    use WithFileUploads;

    // Organisasi
    public $nip;
    public $nama;
    public $subsidiary_id;
    public $divisi;
    public $departemen;
    public $seksi;
    public $posisi;
    public $tgl_masuk;
    public $status_peg = '';
    public $awal_kontrak;
    public $akhir_kontrak;

    // Biodata
    public $nik;
    public $tmpt_lahir;
    public $tgl_lahir;
    public $jenis_kelamin;
    public $alamat;
    public $no_telp;
    public $email;
    public $pend_trkhr = '';
    public $jurusan;
    public $thn_lulus;
    public $nama_ibu;
    public $npwp;
    public $status = '';
    public $jml_ank = 0;

    // Kontak Darurat
    public $nama_kd;
    public $no_kd;
    public $hubungan;

    // File Lampiran
    public $pp;
    public $ktp;
    public $kk;
    public $npwp2;
    public $bpjs_kes;
    public $bpjs_ket;
    public $ttd;

    public function updatedStatusPeg($value)
    {
        if ($value !== 'PKWT') {
            $this->awal_kontrak = null;
            $this->akhir_kontrak = null;
        }
    }

    public function updatedStatus($value)
    {
        if ($value !== 'Kawin' && $value !== 'Cerai') {
            $this->jml_ank = 0;
        }
    }

    protected function rules()
    {
        return [
            // Organisasi
            'nip'           => 'nullable|string|max:50|unique:employees,nip',
            'nama'          => 'required|string|max:255',
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'divisi'        => 'nullable|string|max:100',
            'departemen'    => 'nullable|string|max:100',
            'seksi'         => 'nullable|string|max:100',
            'posisi'        => 'required|string|max:100',
            'tgl_masuk'     => 'nullable|date',
            'status_peg'    => 'required|in:PKWT,PKWTT,-',
            'awal_kontrak'  => 'required_if:status_peg,PKWT|nullable|date',
            'akhir_kontrak' => 'required_if:status_peg,PKWT|nullable|date|after_or_equal:awal_kontrak',

            // Biodata
            'nik'           => 'nullable|string|max:30|unique:employees,nik',
            'tmpt_lahir'    => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string',
            'no_telp'       => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'pend_trkhr'    => 'nullable|string|max:50',
            'jurusan'       => 'nullable|string|max:100',
            'thn_lulus'     => 'nullable|numeric|digits:4',
            'nama_ibu'      => 'nullable|string|max:100',
            'npwp'          => 'nullable|string|max:50',
            'status'        => 'nullable|string|max:50',
            'jml_ank'       => 'nullable|integer|min:0',

            // Kontak Darurat
            'nama_kd'       => 'nullable|string|max:100',
            'no_kd'         => 'nullable|string|max:30',
            'hubungan'      => 'nullable|string|max:50',

            // File Lampiran
            'pp'            => 'nullable|image|max:2048',
            'ktp'           => 'nullable|mimes:pdf|max:3072',
            'kk'            => 'nullable|mimes:pdf|max:3072',
            'npwp2'         => 'nullable|mimes:pdf|max:3072',
            'bpjs_kes'      => 'nullable|mimes:pdf|max:3072',
            'bpjs_ket'      => 'nullable|mimes:pdf|max:3072',
            'ttd'           => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Mapping lokasi simpan berkas
        $fileMap = [
            'pp'       => 'public/foto_profil',
            'ktp'      => 'public/ktp',
            'kk'       => 'public/kk',
            'npwp2'    => 'public/npwp',
            'bpjs_kes' => 'public/bpjs_kes',
            'bpjs_ket' => 'public/bpjs_ket',
            'ttd'      => 'public/ttd',
        ];

        foreach ($fileMap as $field => $folder) {
            if ($this->$field) {
                $storedPath = $this->$field->store($folder);
                $validatedData[$field] = basename($storedPath);
            }
        }

        Employee::create($validatedData);

        session()->flash('success', 'Data karyawan berhasil ditambahkan.');

        return redirect()->route('employees.index');
    }

    public function render()
    {
        return view('hrd.employee.create', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
