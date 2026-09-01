<?php

namespace App\Livewire\Hrd\Employee;

use App\Models\HRD\Employee;
use App\Models\HRD\Subsidiary;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Data Karyawan')]
class EmployeeEdit extends Component
{
    use WithFileUploads;

    public Employee $employee;
    public bool $canEditOrg = false;

    // Organisasi
    public $nip;
    public $nama;
    public $subsidiary_id;
    public $divisi;
    public $departemen;
    public $seksi;
    public $posisi;
    public $tgl_masuk;
    public $status_peg;
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
    public $pend_trkhr;
    public $jurusan;
    public $thn_lulus;
    public $nama_ibu;
    public $npwp;
    public $status;
    public $jml_ank;

    // Kontak Darurat
    public $nama_kd;
    public $no_kd;
    public $hubungan;

    // File Upload Baru
    public $new_pp;
    public $new_ktp;
    public $new_kk;
    public $new_npwp2;
    public $new_bpjs_kes;
    public $new_bpjs_ket;
    public $new_ttd;

    public function mount($employee)
    {
        $this->employee = $employee instanceof Employee ? $employee : Employee::findOrFail($employee);

        // Pengecekan Hak Akses Pengeditan Organisasi
        $user = auth()->user();
        $this->canEditOrg = $user->hasAnyRole(['super-admin', 'holding-admin', 'leader']) || $user->can('employee.edit-org');

        // Mapping Data Karyawan
        $this->fill($this->employee->only([
            'nip',
            'nama',
            'subsidiary_id',
            'divisi',
            'departemen',
            'seksi',
            'posisi',
            'tgl_masuk',
            'status_peg',
            'awal_kontrak',
            'akhir_kontrak',
            'nik',
            'tmpt_lahir',
            'tgl_lahir',
            'jenis_kelamin',
            'alamat',
            'no_telp',
            'email',
            'pend_trkhr',
            'jurusan',
            'thn_lulus',
            'nama_ibu',
            'npwp',
            'status',
            'jml_ank',
            'nama_kd',
            'no_kd',
            'hubungan'
        ]));
    }

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
            // Validation Organisasi (Hanya divalidasi jika user berhak mengedit)
            'nip'           => 'nullable|string|max:50|unique:employees,nip,' . $this->employee->id,
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

            // Validation Biodata
            'nik'           => 'nullable|string|max:30|unique:employees,nik,' . $this->employee->id,
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

            // File Upload
            'new_pp'        => 'nullable|image|max:2048',
            'new_ktp'       => 'nullable|mimes:pdf|max:3072',
            'new_kk'        => 'nullable|mimes:pdf|max:3072',
            'new_npwp2'     => 'nullable|mimes:pdf|max:3072',
            'new_bpjs_kes'  => 'nullable|mimes:pdf|max:3072',
            'new_bpjs_ket'  => 'nullable|mimes:pdf|max:3072',
            'new_ttd'       => 'nullable|image|max:2048',
        ];
    }

    public function update()
    {
        $validatedData = $this->validate();

        // 1. Jika tidak berhak edit organisasi, keluarkan kolom organisasi dari array update
        if (!$this->canEditOrg) {
            $organizationFields = ['nip', 'nama', 'subsidiary_id', 'divisi', 'departemen', 'seksi', 'posisi', 'tgl_masuk', 'status_peg', 'awal_kontrak', 'akhir_kontrak'];
            foreach ($organizationFields as $field) {
                unset($validatedData[$field]);
            }
        }

        // 2. Daftar mapping property temp upload ke nama kolom asli di database
        $fileUploads = [
            'new_pp'       => ['column' => 'pp',       'folder' => 'public/foto_profil'],
            'new_ktp'      => ['column' => 'ktp',      'folder' => 'public/ktp'],
            'new_kk'       => ['column' => 'kk',       'folder' => 'public/kk'],
            'new_npwp2'    => ['column' => 'npwp2',    'folder' => 'public/npwp'],
            'new_bpjs_kes' => ['column' => 'bpjs_kes', 'folder' => 'public/bpjs_kes'],
            'new_bpjs_ket' => ['column' => 'bpjs_ket', 'folder' => 'public/bpjs_ket'],
            'new_ttd'      => ['column' => 'ttd',      'folder' => 'public/ttd'],
        ];

        foreach ($fileUploads as $property => $meta) {
            if (!empty($this->$property)) {
                // Hapus file lama jika ada
                if ($this->employee->{$meta['column']}) {
                    Storage::delete($meta['folder'] . '/' . $this->employee->{$meta['column']});
                }

                // Simpan file baru
                $storedPath = $this->$property->store($meta['folder']);
                // Masukkan nama file ke kolom database asli
                $validatedData[$meta['column']] = basename($storedPath);
            }

            // SANGAT PENTING: Hapus key temporary `new_*` agar Eloquent tidak mengirimnya ke SQL query
            unset($validatedData[$property]);
        }

        // 3. Eksekusi Update ke Database
        $this->employee->update($validatedData);

        session()->flash('success', 'Data karyawan berhasil diperbarui.');

        return $this->redirectRoute('employees.show', ['employee' => $this->employee->id], navigate: true);
    }
    
    public function render()
    {
        return view('hrd.employee.employee-edit', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
