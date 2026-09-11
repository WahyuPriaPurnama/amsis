<?php
namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Purchasing\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorPortal extends Component
{
    use WithFileUploads;

    public $vendor;
    
    // Properti form edit profil/berkas
    public $company_name, $address, $nib, $npwp;
    public $bank_name, $bank_account_number, $bank_account_holder;
    public $pic_name, $pic_phone;
    public $nib_file, $npwp_file, $certificate_file;

    public function mount()
    {
        $this->vendor = Vendor::where('user_id', Auth::id())->first();

        if (!$this->vendor) {
            abort(403, 'Unauthorized access to Vendor Portal.');
        }

        // Sinkronkan data awal ke properti form
        $this->company_name = $this->vendor->company_name;
        $this->address = $this->vendor->address;
        $this->nib = $this->vendor->nib;
        $this->npwp = $this->vendor->npwp;
        $this->bank_name = $this->vendor->bank_name;
        $this->bank_account_number = $this->vendor->bank_account_number;
        $this->bank_account_holder = $this->vendor->bank_account_holder;
        $this->pic_name = $this->vendor->pic_name;
        $this->pic_phone = $this->vendor->pic_phone;
    }

    public function updateProfile()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'nib' => 'required|string|max:50',
            'npwp' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:255',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:20',
            'nib_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'npwp_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $data = [
            'company_name' => $this->company_name,
            'address' => $this->address,
            'nib' => $this->nib,
            'npwp' => $this->npwp,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_holder' => $this->bank_account_holder,
            'pic_name' => $this->pic_name,
            'pic_phone' => $this->pic_phone,
        ];

        // Tangani upload file baru jika ada
        if ($this->nib_file) {
            Storage::disk('public')->delete($this->vendor->nib_file);
            $data['nib_file'] = $this->nib_file->store('vendors/nib', 'public');
        }

        if ($this->npwp_file) {
            Storage::disk('public')->delete($this->vendor->npwp_file);
            $data['npwp_file'] = $this->npwp_file->store('vendors/npwp', 'public');
        }

        if ($this->certificate_file) {
            if ($this->vendor->certificate_file) {
                Storage::disk('public')->delete($this->vendor->certificate_file);
            }
            $data['certificate_file'] = $this->certificate_file->store('vendors/certificates', 'public');
        }

        $this->vendor->update($data);

        session()->flash('message', 'Profil dan dokumen perusahaan berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.vendor.vendor-portal')->layout('layouts.app');
    }
}