<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\Asset;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Aset')]
class AssetShow extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->asset = $asset->load(['subsidiary', 'user']);
    }

    public function deleteAsset()
    {
        try {
            // Hapus berkas terkait dari storage jika ada
            $fileFields = ['delivery_receipt', 'manual_book', 'photo', 'attachment'];
            foreach ($fileFields as $field) {
                if ($this->asset->$field) {
                    Storage::delete('public/assets/' . $field . '/' . $this->asset->$field);
                }
            }

            $this->asset->delete();

            session()->flash('success', 'Data aset berhasil dihapus.');
            return $this->redirectRoute('asset.index', navigate: true);
        } catch (\Throwable $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus data aset.');
        }
    }

    public function render()
    {
        return view('hrd.asset.show');
    }
}
