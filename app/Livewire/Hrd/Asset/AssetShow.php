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
        $user = auth()->user();

        // 1. Cek Permission: User wajib memiliki permission 'asset.view'
        if (!$user->can('asset.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat data aset.');
        }

        // 2. Batasi akses Subsidiary:
        // Jika bukan super-admin/holding-admin, hanya boleh akses asset milik subsidiary-nya sendiri
        if (
            !$user->hasRole(['super-admin', 'holding-admin']) &&
            $user->subsidiary_id !== $asset->subsidiary_id
        ) {
            abort(403, 'Anda tidak memiliki akses ke data aset subsidiary ini.');
        }

        $this->asset = $asset->load(['subsidiary', 'user']);
    }

    /**
     * Helper privat untuk menentukan path lokasi file di storage
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
        // Pastikan user juga punya izin view untuk download file
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
        // Proteksi Otorisasi aksi hapus
        if (!auth()->user()->can('asset.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Asset ini.');
        }

        try {
            $fileFields = ['attachment', 'photo', 'delivery_receipt', 'manual_book'];

            foreach ($fileFields as $field) {
                $fileName = $this->asset->$field;

                if ($fileName) {
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
