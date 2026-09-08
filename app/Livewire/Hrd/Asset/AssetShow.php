<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AssetShow extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $user = auth()->user();

        // Batasi akses: hanya super-admin/holding-admin atau asset milik subsidiary user
        if (
            !$user->hasRole(['super-admin', 'holding-admin']) &&
            $user->subsidiary_id !== $asset->subsidiary_id
        ) {
            abort(403, 'Anda tidak memiliki izin untuk melihat Asset ini.');
        }

        $this->asset = $asset->load(['subsidiary', 'user']);
    }

    /**
     * Method Livewire untuk melihat/mengunduh berkas secara aman
     */
    public function downloadFile($field)
    {
        $allowedFields = ['photo', 'attachment', 'delivery_receipt', 'manual_book'];

        if (!in_array($field, $allowedFields)) {
            abort(403, 'Aksi tidak sah.');
        }

        $fileName = $this->asset->$field;

        if (!$fileName) {
            session()->flash('error', 'File tidak ditemukan.');
            return;
        }
        $filePath = ($field === 'photo') ? 'assets/photo/' . $fileName : 'assets/' . $field . '/' . $fileName;

        if (!Storage::disk('public')->exists($filePath)) {
            session()->flash('error', 'File tidak ditemukan di storage.');
            return;
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $cleanAssetName = \Str::slug($this->asset->name);
        $customName = $cleanAssetName . '-' . $field . '.' . $extension;

        return Storage::disk('public')->download($filePath, $customName);
    }
    public function deleteAsset()
    {
        try {
            $fileFields = ['attachment', 'photo', 'delivery_receipt', 'manual_book'];
            foreach ($fileFields as $field) {
                if ($this->asset->$field && Storage::disk('public')->exists($this->asset->$field)) {
                    Storage::disk('public')->delete($this->asset->$field);
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
