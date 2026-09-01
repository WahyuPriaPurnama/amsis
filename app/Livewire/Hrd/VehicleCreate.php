<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\Subsidiary;
use App\Models\HRD\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Tambah Kendaraan')]
class VehicleCreate extends Component
{
    use WithFileUploads;

    // Properti Form Input
    public $jenis_kendaraan = '';
    public $kategori = '';
    public $subsidiary_id = '';
    public $tgl_perolehan = null;
    public $pengguna = '';
    public $nama_warna = '';
    public $warna = '#000000';
    public $tahun = null;
    public $atas_nama = '';
    public $nopol = '';
    public $tgl_service = null;
    public $km_akhir = null;
    public $no_rangka = '';
    public $no_bpkb = '';
    public $no_mesin = '';
    public $stnk = null;
    public $pajak = null;
    public $kir = null;
    public $j_asuransi = '';
    public $p_asuransi = '';
    public $no_asuransi = '';
    public $jth_tempo = null;
    public $kondisi = '';
    public $keterangan = '';

    // Properti Upload Berkas Lampiran
    public $foto;
    public $f_stnk;
    public $f_pajak;
    public $f_kir;
    public $qr;
    public $f_polis;
    public $f_service;

    protected function rules()
    {
        return [
            'jenis_kendaraan' => 'required|string|max:255',
            'kategori'        => 'required|string',
            'subsidiary_id'   => 'required|exists:subsidiaries,id',
            'tgl_perolehan'   => 'nullable|date',
            'pengguna'        => 'nullable|string|max:255',
            'nama_warna'      => 'nullable|string|max:100',
            'warna'           => 'nullable|string|max:50',
            'tahun'           => 'nullable|numeric',
            'atas_nama'       => 'nullable|string|max:255',
            'nopol'           => 'required|string|max:20',
            'tgl_service'     => 'nullable|date',
            'km_akhir'        => 'nullable|numeric',
            'no_rangka'       => 'nullable|string|max:255',
            'no_bpkb'         => 'nullable|string|max:255',
            'no_mesin'        => 'nullable|string|max:255',
            'stnk'            => 'nullable|date',
            'pajak'           => 'nullable|date',
            'kir'             => 'nullable|date',
            'j_asuransi'      => 'nullable|string|max:255',
            'p_asuransi'      => 'nullable|string|max:255',
            'no_asuransi'     => 'nullable|string|max:255',
            'jth_tempo'       => 'nullable|date',
            'kondisi'         => 'required|string',
            'keterangan'      => 'nullable|string',

            // Validasi File Uploads
            'foto'            => 'nullable|image|max:2048',
            'f_stnk'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'f_pajak'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'f_kir'           => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'qr'              => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'f_polis'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'f_service'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Simpan File Lampiran jika ada yang diunggah
        $files = ['foto', 'f_stnk', 'f_pajak', 'f_kir', 'qr', 'f_polis', 'f_service'];

        foreach ($files as $fileKey) {
            if ($this->$fileKey) {
                $storedPath = $this->$fileKey->store('public/vehicle/' . $fileKey);
                $validatedData[$fileKey] = basename($storedPath);
            }
        }

        Vehicle::create($validatedData);

        session()->flash('success', 'Data kendaraan berhasil disimpan.');

        return $this->redirectRoute('vehicles.index', navigate: true);
    }

    public function render()
    {
        return view('hrd.vehicle.create', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
