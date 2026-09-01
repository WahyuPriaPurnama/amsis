<?php

namespace App\Livewire\Hrd\Vehicle;

use App\Models\HRD\Vehicle;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VehicleShow extends Component
{
    public Vehicle $vehicle;

    public function mount($vehicle)
    {
        $this->vehicle = $vehicle instanceof Vehicle ? $vehicle : Vehicle::with('subsidiary')->findOrFail($vehicle);
    }

    public function deleteVehicle()
    {
        try {
            // Hapus file lampiran dari storage jika ada
            $files = ['foto', 'f_stnk', 'f_pajak', 'f_kir', 'qr', 'f_polis', 'f_service'];
            foreach ($files as $fileKey) {
                if ($this->vehicle->$fileKey) {
                    Storage::delete('public/vehicle/' . $fileKey . '/' . $this->vehicle->$fileKey);
                }
            }

            // Hapus data dari database
            $this->vehicle->delete();

            session()->flash('success', 'Data kendaraan berhasil dihapus.');
            return $this->redirectRoute('vehicles.index', navigate: true);
        } catch (\Throwable $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            // Pengecekan relasi foreign key
            if ($errorCode == 23000 || str_contains($errorMessage, '23000') || str_contains($errorMessage, '1451')) {
                session()->flash('error', 'Kendaraan tidak dapat dihapus karena masih terhubung dengan riwayat transaksi / service.');
                return;
            }

            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }


    public function render()
    {
        return view('hrd.vehicle.vehicle-show')
            ->title("Detail " . $this->vehicle->jenis_kendaraan);
    }
}
