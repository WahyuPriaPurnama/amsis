<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Aset')]
class AssetEdit extends Component
{
    use WithFileUploads;

    public Asset $asset;

    // Properti Form Input (Urutan Sama Dengan Create)
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

    // Properti Upload Berkas Baru
    public $new_delivery_receipt;
    public $new_manual_book;
    public $new_photo;
    public $new_attachment;

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
        $this->subsidiary_id = $asset->subsidiary_id;
        $this->code = $asset->code;
        $this->name = $asset->name;
        $this->location = $asset->location;
        $this->quantity = $asset->quantity;
        $this->unit = $asset->unit;
        $this->condition = $asset->condition;
        $this->owner = $asset->owner;
        $this->category = $asset->category;
        $this->accounting_code = $asset->accounting_code;
        $this->usage_date = $asset->usage_date;
        $this->purchase_date = $asset->purchase_date;
        $this->purchase_value = $asset->purchase_value;
        $this->depreciation_value = $asset->depreciation_value;
        $this->total_value = $asset->total_value;
        $this->useful_life = $asset->useful_life;
        $this->description = $asset->description;
    }

    protected function rules()
    {
        return [
            'subsidiary_id'        => 'required|exists:subsidiaries,id',
            'code'                 => 'required|string|max:100|unique:assets,code,' . $this->asset->id,
            'name'                 => 'required|string|max:255',
            'location'             => 'required|string|max:255',
            'quantity'             => 'required|integer|min:1',
            'unit'                 => 'required|string|max:50',
            'condition'            => 'required|string',
            'owner'                => 'required|string',
            'category'             => 'required|string',
            'accounting_code'      => 'nullable|string|max:100',
            'usage_date'           => 'nullable|date',
            'purchase_date'        => 'nullable|date',
            'purchase_value'       => 'nullable|numeric|min:0',
            'depreciation_value'   => 'nullable|numeric|min:0',
            'total_value'          => 'nullable|numeric|min:0',
            'useful_life'          => 'nullable|integer|min:0',
            'description'          => 'nullable|string',

            'new_delivery_receipt' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_manual_book'      => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'new_photo'            => 'nullable|image|max:2048',
            'new_attachment'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function update()
    {
        $validatedData = $this->validate();

        // Konversi string kosong ('') pada tanggal menjadi null agar tidak error 1292
        $validatedData['usage_date']    = $this->usage_date ?: null;
        $validatedData['purchase_date'] = $this->purchase_date ?: null;

        // Pemetaan nama kolom DB => nama properti Livewire sementara
        $fileMapping = [
            'delivery_receipt' => 'new_delivery_receipt',
            'manual_book'      => 'new_manual_book',
            'photo'            => 'new_photo',
            'attachment'       => 'new_attachment',
        ];

        foreach ($fileMapping as $dbColumn => $property) {
            if ($this->$property) {
                // Hapus berkas lama di storage jika ada
                if ($this->asset->$dbColumn) {
                    Storage::delete('public/assets/' . $dbColumn . '/' . $this->asset->$dbColumn);
                }

                // Simpan berkas baru & masukkan ke nama kolom DB yang benar
                $path = $this->$property->store('public/assets/' . $dbColumn);
                $validatedData[$dbColumn] = basename($path);
            }

            // KUNCI PERBAIKAN: Hapus properti sementara dari array $validatedData
            // agar Eloquent tidak mencoba menyimpannya sebagai kolom DB
            unset($validatedData[$property]);
        }

        // Simpan perubahan ke database
        $this->asset->update($validatedData);

        session()->flash('success', 'Data aset berhasil diperbarui.');

        return $this->redirectRoute('asset.index', navigate: true);
    }

    public function render()
    {
        return view('hrd.asset.asset-edit', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
