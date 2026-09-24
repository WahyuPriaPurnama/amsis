<?php
namespace App\Livewire\Purchasing\Vendor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Purchasing\Vendor;

class VendorRegister extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    
    public $company_name, $address, $nib, $npwp;
    
    public $has_halal = false, $has_haccp = false, $skp_number;

    public $bank_name, $bank_account_number, $bank_account_holder, $pic_name, $pic_email, $pic_phone;
    
    public $nib_file, $npwp_file, $certificate_file;

    public function render()
    {
        return view('livewire.purchasing.vendor.vendor-register');
    }

    public function increaseStep()
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }


    public function decreaseStep()
    {
        $this->currentStep--;
    }


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


    public function submit()
    {
        $this->validate([
            'nib_file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'npwp_file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);


        $nibPath = $this->nib_file->store('vendor-documents', 'public');
        $npwpPath = $this->npwp_file->store('vendor-documents', 'public');
        $certPath = $this->certificate_file ? $this->certificate_file->store('vendor-documents', 'public') : null;


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

        return redirect()->route('vendor.success')->with('message', 'Registrasi berhasil! Berkas Anda sedang direview oleh admin.');
    }
}