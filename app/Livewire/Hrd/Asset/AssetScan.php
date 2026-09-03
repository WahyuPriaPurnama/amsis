<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Gunakan layout guest/publik agar tidak ada menu admin yang muncul
#[Layout('layouts.guest')]
#[Title('Informasi Aset')]
class AssetScan extends Component
{
    public Asset $asset;

    public function mount($code)
    {
        // Cari aset berdasarkan kode aset yang di-scan, jika tidak ada munculkan 404
        $this->asset = Asset::with(['subsidiary'])->where('code', $code)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.hrd.asset.asset-scan');
    }
}
