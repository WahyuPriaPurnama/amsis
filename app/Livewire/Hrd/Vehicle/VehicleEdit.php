<?php

namespace App\Livewire\Hrd\Vehicle;

use App\Models\HRD\Subsidiary;
use App\Models\HRD\Vehicle;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Data Kendaraan')]
class VehicleEdit extends Component
{
    use WithFileUploads;

    public Vehicle $vehicle;

    // Form Inputs
    public $jenis_kendaraan;
    public $kategori;
    public $subsidiary_id;
    public $tgl_perolehan;
    public $pengguna;
    public $nama_warna;
    public $warna;
    public $tahun;
    public $atas_nama;
    public $nopol;
    public $tgl_service;
    public $km_akhir;
    public $no_rangka;
    public $no_bpkb;
    public $no_mesin;
    public $stnk;
    public $pajak;
    public $kir;
    public $j_asuransi;
    public $p_asuransi;
    public $no_asuransi;
    public $jth_tempo;
    public $kondisi;
    public $keterangan;

    // Upload Lampiran Baru (Temporary Uploads)
    public $new_foto;
    public $new_f_stnk;
    public $new_f_pajak;
    public $new_f_kir;
    public $new_qr;
    public $new_f_polis;
    public $new_f_service;

    public function mount($vehicle)
    {
        $this->vehicle = $vehicle instanceof Vehicle ? $vehicle : Vehicle::findOrFail($vehicle);

        $this->jenis_kendaraan = $this->vehicle->jenis_kendaraan;
        $this->kategori        = $this->vehicle->kategori;
        $this->subsidiary_id   = $this->vehicle->subsidiary_id;
        $this->tgl_perolehan   = $this->vehicle->tgl_perolehan;
        $this->pengguna        = $this->vehicle->pengguna;
        $this->nama_warna      = $this->vehicle->nama_warna;
        $this->warna           = $this->vehicle->warna ?? '#000000';
        $this->tahun           = $this->vehicle->tahun;
        $this->atas_nama       = $this->vehicle->atas_nama;
        $this->nopol           = $this->vehicle->nopol;
        $this->tgl_service     = $this->vehicle->tgl_service;
        $this->km_akhir        = $this->vehicle->km_akhir;
        $this->no_rangka       = $this->vehicle->no_rangka;
        $this->no_bpkb         = $this->vehicle->no_bpkb;
        $this->no_mesin        = $this->vehicle->no_mesin;
        $this->stnk            = $this->vehicle->stnk;
        $this->pajak           = $this->vehicle->pajak;
        $this->kir             = $this->vehicle->kir;
        $this->j_asuransi      = $this->vehicle->j_asuransi;
        $this->p_asuransi      = $this->vehicle->p_asuransi;
        $this->no_asuransi     = $this->vehicle->no_asuransi;
        $this->jth_tempo       = $this->vehicle->jth_tempo;
        $this->kondisi         = $this->vehicle->kondisi;
        $this->keterangan      = $this->vehicle->keterangan;
    }

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

            // New File Uploads
            'new_foto'        => 'nullable|image|max:2048',
            'new_f_stnk'      => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_f_pajak'     => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_f_kir'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_qr'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_f_polis'     => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_f_service'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function update()
    {
        $validatedData = $this->validate();

        // Mapping file temporary `new_*` ke nama kolom database asli
        $fileUploads = [
            'new_foto'      => 'foto',
            'new_f_stnk'    => 'f_stnk',
            'new_f_pajak'   => 'f_pajak',
            'new_f_kir'     => 'f_kir',
            'new_qr'        => 'qr',
            'new_f_polis'   => 'f_polis',
            'new_f_service' => 'f_service',
        ];

        foreach ($fileUploads as $tempProp => $dbColumn) {
            if ($this->$tempProp) {
                // Hapus berkas lama dari storage jika ada
                if ($this->vehicle->$dbColumn) {
                    Storage::delete('public/vehicle/' . $dbColumn . '/' . $this->vehicle->$dbColumn);
                }

                // Simpan berkas baru
                $storedPath = $this->$tempProp->store('public/vehicle/' . $dbColumn);
                $validatedData[$dbColumn] = basename($storedPath);
            }

            // Hapus key temporary agar tidak mengganggu query SQL update
            unset($validatedData[$tempProp]);
        }

        $this->vehicle->update($validatedData);

        session()->flash('success', 'Data kendaraan berhasil diperbarui.');

        return $this->redirectRoute('vehicles.show', ['vehicle' => $this->vehicle->id], navigate: true);
    }

    public function render()
    {
        return view('hrd.vehicle.vehicle-edit', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}