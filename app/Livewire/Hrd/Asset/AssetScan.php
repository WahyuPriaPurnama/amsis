<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Informasi Aset')]
class AssetScan extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        // Jika user belum login, simpan URL halaman ini agar setelah login diarahkan kembali ke sini
        if (!auth()->check()) {
            session(['url.intended' => URL::current()]);
        }

        // Cari aset berdasarkan kode/ID yang di-scan, load relasi subsidiary
        $this->asset = $asset->load(['subsidiary']);
    }

    public function render()
    {
        return view('livewire.hrd.asset.asset-scan');
    }
}
