<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class AssetEdit extends Component
{
    use WithFileUploads;

    public Asset $asset;

    // Properti Form Input
    public $subsidiary_id;
    public $code;
    public $name;
    public $location;
    public $quantity;
    public $unit;
    public $condition;
    public $owner;
    public $category;
    public $accounting_code;
    public $usage_date;
    public $purchase_date;
    public $purchase_value;
    public $depreciation_value;
    public $total_value;
    public $useful_life;
    public $description;

    // Properti Berkas Lama dari Database
    public $delivery_receipt;
    public $manual_book;
    public $photo;
    public $attachment;

    // Properti Upload Berkas Baru
    public $new_delivery_receipt;
    public $new_manual_book;
    public $new_photo;
    public $new_attachment;

    public function mount(Asset $asset)
    {
        // Pengecekan Permission murni 'asset.edit'
        if (!auth()->user()->can('asset.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data aset ini.');
        }

        $this->asset = $asset;
        $this->subsidiary_id = $asset->subsidiary_id;
        $this->code          = $asset->code;
        $this->name          = $asset->name;
        $this->location      = $asset->location;
        $this->quantity      = $asset->quantity;
        $this->unit          = $asset->unit;
        $this->condition     = $asset->condition;
        $this->owner         = $asset->owner;
        $this->category      = $asset->category;
        $this->accounting_code   = $asset->accounting_code;
        $this->usage_date        = $asset->usage_date;
        $this->purchase_date     = $asset->purchase_date;
        $this->purchase_value    = $asset->purchase_value;
        $this->depreciation_value = $asset->depreciation_value;
        $this->total_value       = $asset->total_value;
        $this->useful_life       = $asset->useful_life;
        $this->description       = $asset->description;

        $this->delivery_receipt = $asset->delivery_receipt;
        $this->manual_book      = $asset->manual_book;
        $this->photo            = $asset->photo;
        $this->attachment       = $asset->attachment;
    }

    protected function rules()
    {
        return [
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'code' => [
                'required',
                'string',
                'max:100',
                // Kode unik berdasarkan subsidiary_id & mengabaikan aset yang sedang diedit
                Rule::unique('assets', 'code')
                    ->ignore($this->asset->id)
                    ->where(function ($query) {
                        return $query->where('subsidiary_id', $this->subsidiary_id);
                    }),
            ],
            'name'               => 'required|string|max:255',
            'location'           => 'required|string|max:255',
            'quantity'           => 'required|integer|min:1',
            'unit'               => 'required|string|max:50',
            'condition'          => 'required|string',
            'owner'              => 'required|string',
            'category'           => 'required|string',
            'accounting_code'    => 'nullable|string|max:100',
            'usage_date'         => 'nullable|date',
            'purchase_date'      => 'nullable|date',
            'purchase_value'     => 'nullable|numeric|min:0',
            'depreciation_value' => 'nullable|numeric|min:0',
            'total_value'        => 'nullable|numeric|min:0',
            'useful_life'        => 'nullable|integer|min:0',
            'description'        => 'nullable|string',

            'new_delivery_receipt' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
            'new_manual_book'      => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
            'new_photo'            => 'nullable|image|max:10240',
            'new_attachment'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }

    private function getStorageFolder(string $field): string
    {
        return ($field === 'photo') ? 'assets/photo' : 'assets/' . $field;
    }

    public function update()
    {
        if (!auth()->user()->can('asset.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data aset.');
        }

        $validatedData = $this->validate();

        $validatedData['usage_date']    = $this->usage_date ?: null;
        $validatedData['purchase_date'] = $this->purchase_date ?: null;

        $fileMapping = [
            'delivery_receipt' => 'new_delivery_receipt',
            'manual_book'      => 'new_manual_book',
            'photo'            => 'new_photo',
            'attachment'       => 'new_attachment',
        ];

        foreach ($fileMapping as $dbColumn => $property) {
            if ($this->$property) {
                $folder = $this->getStorageFolder($dbColumn);

                // 1. Hapus berkas lama jika ada di storage
                if ($this->asset->$dbColumn) {
                    $oldFilePath = $folder . '/' . $this->asset->$dbColumn;
                    if (Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                }

                // 2. Simpan file baru & simpan HANYA nama filenya (hashName) ke DB
                $this->$property->store($folder, 'public');
                $validatedData[$dbColumn] = $this->$property->hashName();
            } else {
                unset($validatedData[$dbColumn]);
            }

            unset($validatedData[$property]);
        }

        unset(
            $validatedData['new_delivery_receipt'],
            $validatedData['new_manual_book'],
            $validatedData['new_photo'],
            $validatedData['new_attachment']
        );

        $this->asset->update($validatedData);

        session()->flash('success', 'Data aset berhasil diperbarui.');

        return $this->redirectRoute('asset.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.hrd.asset.asset-edit', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ])->title('Edit Aset - ' . ($this->asset->name ?? ''));
    }
}
