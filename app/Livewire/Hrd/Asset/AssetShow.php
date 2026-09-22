<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AssetShow extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        // Otorisasi berbasis permission 'asset.view'
        if (!auth()->user()->can('asset.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat data aset ini.');
        }

        $this->asset = $asset->load(['subsidiary', 'user']);
    }

    /**
     * Helper privat untuk menentukan path lokasi file di storage
     * Menggabungkan folder kategori dengan nama file dari database
     */
    private function getStoragePath(string $field, string $fileName): string
    {
        return ($field === 'photo')
            ? 'assets/photo/' . $fileName
            : 'assets/' . $field . '/' . $fileName;
    }

    /**
     * Method Livewire untuk melihat/mengunduh berkas secara aman
     */
    public function downloadFile($field)
    {
        // Otorisasi unduh berkas mengikuti permission 'asset.view'
        if (!auth()->user()->can('asset.view')) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh berkas ini.');
        }

        $allowedFields = ['photo', 'attachment', 'delivery_receipt', 'manual_book'];

        if (!in_array($field, $allowedFields)) {
            abort(403, 'Aksi tidak sah.');
        }

        $fileName = $this->asset->$field;

        if (!$fileName) {
            session()->flash('error', 'File tidak ditemukan.');
            return;
        }

        // Menyusun path lengkap folder + nama file
        $filePath = $this->getStoragePath($field, $fileName);

        if (!Storage::disk('public')->exists($filePath)) {
            session()->flash('error', 'File tidak ditemukan di storage.');
            return;
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $cleanAssetName = Str::slug($this->asset->name);
        $customName = $cleanAssetName . '-' . $field . '.' . $extension;

        return Storage::disk('public')->download($filePath, $customName);
    }

    public function deleteAsset()
    {
        // Otorisasi hapus berbasis permission 'asset.delete'
        if (!auth()->user()->can('asset.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Asset ini.');
        }

        try {
            $fileFields = ['attachment', 'photo', 'delivery_receipt', 'manual_book'];

            foreach ($fileFields as $field) {
                $fileName = $this->asset->$field;

                if ($fileName) {
                    // Menyusun path lengkap folder + nama file untuk dihapus
                    $filePath = $this->getStoragePath($field, $fileName);

                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
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
        return view('livewire.hrd.asset.asset-show')
            ->title('Detail Aset - ' . ($this->asset->name ?? ''));
    }
}
