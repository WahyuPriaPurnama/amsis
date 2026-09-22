<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Input Aset')]
class AssetCreate extends Component
{
    use WithFileUploads;

    // Properti Form Input
    public $subsidiary_id = '';
    public $code = '';
    public $name = '';
    public $location = '';
    public $quantity = 1;
    public $unit = '';
    public $condition = 'Baik';
    public $owner = 'Umum';
    public $category = 'Tanah & Bangunan';
    public $accounting_code = '';
    public $usage_date = null;
    public $purchase_date = null;
    public $purchase_value = 0;
    public $depreciation_value = 0;
    public $total_value = 0;
    public $useful_life = 0;
    public $description = '';

    // Properti Upload Berkas
    public $delivery_receipt;
    public $manual_book;
    public $photo;
    public $attachment;

    public function mount()
    {
        // Pengecekan Permission murni 'asset.create'
        if (!auth()->user()->can('asset.create')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah data aset.');
        }
    }

    protected function rules()
    {
        return [
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'code' => [
                'required',
                'string',
                'max:100',
                // Kode boleh sama jika subsidiary_id berbeda
                Rule::unique('assets', 'code')->where(function ($query) {
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

            'delivery_receipt'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
            'manual_book'        => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
            'photo'              => 'nullable|image|max:10240',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }

    private function getStorageFolder(string $field): string
    {
        return ($field === 'photo') ? 'assets/photo' : 'assets/' . $field;
    }

    public function save()
    {
        if (!auth()->user()->can('asset.create')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah data aset.');
        }

        $validatedData = $this->validate();

        $validatedData['user_id'] = auth()->id();

        // Konversi string kosong ('') pada tanggal menjadi null
        $validatedData['usage_date']    = $this->usage_date ?: null;
        $validatedData['purchase_date'] = $this->purchase_date ?: null;

        // Simpan berkas dan simpan HANYA nama filenya (hashName) ke DB
        $fileFields = ['delivery_receipt', 'manual_book', 'photo', 'attachment'];

        foreach ($fileFields as $field) {
            if ($this->$field) {
                $folder = $this->getStorageFolder($field);

                // Upload berkas ke storage
                $this->$field->store($folder, 'public');

                // Ambil hanya nama file (misal: "xHUYEj7hyanCS2pCnfqCKOBj6WnL5i5xqg33CTxg.jpg")
                $validatedData[$field] = $this->$field->hashName();
            } else {
                $validatedData[$field] = null;
            }
        }

        // Generate QR Code otomatis
        $validatedData['qr_code'] = $this->code;

        Asset::create($validatedData);

        session()->flash('success', 'Data aset berhasil ditambahkan.');

        return $this->redirectRoute('asset.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.hrd.asset.asset-create', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
