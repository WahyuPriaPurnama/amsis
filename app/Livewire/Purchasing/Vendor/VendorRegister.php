<?php
namespace App\Livewire\Purchasing\Vendor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Purchasing\Vendor;
use Illuminate\Support\Facades\Storage;

class VendorRegister extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    
    // Step 1: Profil & Legalitas
    public $company_name, $address, $nib, $npwp;
    
    // Step 2: Kualitas & Sertifikasi
    public $has_halal = false, $has_haccp = false, $skp_number;
    
    // Step 3: Rekening & Kontak PIC
    public $bank_name, $bank_account_number, $bank_account_holder, $pic_name, $pic_email, $pic_phone;
    
    // Step 4: Dokumen (File Uploads)
    public $nib_file, $npwp_file, $certificate_file;

    public function render()
    {
        return view('livewire.purchasing.vendor.vendor-register');
    }

    // Pindah ke step berikutnya dengan validasi per step
    public function increaseStep()
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    // Kembali ke step sebelumnya
    public function decreaseStep()
    {
        $this->currentStep--;
    }

    // Validasi dinamis berdasarkan step yang sedang aktif
    protected function validateCurrentStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'company_name' => 'required|string|max:255',
                'address' => 'required|string',
                'nib' => 'required|string|unique:vendors,nib',
                'npwp' => 'required|string|unique:vendors,npwp',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'has_halal' => 'boolean',
                'has_haccp' => 'boolean',
                'skp_number' => 'nullable|string',
            ]);
        } elseif ($this->currentStep == 3) {
            $this->validate([
                'bank_name' => 'required|string',
                'bank_account_number' => 'required|string',
                'bank_account_holder' => 'required|string',
                'pic_name' => 'required|string',
                'pic_email' => 'required|email|unique:vendors,pic_email',
                'pic_phone' => 'required|string',
            ]);
        }
    }

    // Proses Submit Final
    public function submit()
    {
        $this->validate([
            'nib_file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'npwp_file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Simpan file ke storage (folder public/vendor-documents)
        $nibPath = $this->nib_file->store('vendor-documents', 'public');
        $npwpPath = $this->npwp_file->store('vendor-documents', 'public');
        $certPath = $this->certificate_file ? $this->certificate_file->store('vendor-documents', 'public') : null;

        // Simpan data ke database
        Vendor::create([
            'company_name' => $this->company_name,
            'address' => $this->address,
            'nib' => $this->nib,
            'npwp' => $this->npwp,
            'has_halal' => $this->has_halal,
            'has_haccp' => $this->has_haccp,
            'skp_number' => $this->skp_number,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_holder' => $this->bank_account_holder,
            'pic_name' => $this->pic_name,
            'pic_email' => $this->pic_email,
            'pic_phone' => $this->pic_phone,
            'nib_file' => $nibPath,
            'npwp_file' => $npwpPath,
            'certificate_file' => $certPath,
            'status' => 'pending',
        ]);

        // TODO: Trigger Kirim Email Konfirmasi (Pending Review) ke vendor

        // Redirect atau tampilkan pesan sukses
        return redirect()->route('vendor.success')->with('message', 'Registrasi berhasil! Berkas Anda sedang direview oleh admin.');
    }
}