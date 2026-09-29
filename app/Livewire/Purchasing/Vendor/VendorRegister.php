<?php

namespace App\Livewire\Purchasing\Vendor;

use App\Models\Purchasing\Vendor;
use Livewire\Component;
use Livewire\WithFileUploads;

class VendorRegister extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;


    public string $company_name = '';
    public string $address = '';
    public string $nib = '';
    public string $npwp = '';


    public bool $has_halal = false;
    public bool $has_haccp = false;
    public ?string $skp_number = null;


    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_holder = '';
    public string $pic_name = '';
    public string $pic_email = '';
    public string $pic_phone = '';


    public $nib_file;
    public $npwp_file;
    public $certificate_file;

    public function render()
    {
        return view('livewire.purchasing.vendor.vendor-register');
    }

    public function increaseStep(): void
    {
        $this->validateCurrentStep();
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function decreaseStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'company_name' => 'required|string|max:255',
                'address'      => 'required|string',
                'nib'          => 'required|string|max:100|unique:vendors,nib',
                'npwp'         => 'required|string|max:100|unique:vendors,npwp',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'has_halal'  => 'boolean',
                'has_haccp'  => 'boolean',
                'skp_number' => 'nullable|string|max:255',
            ]);
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'bank_name'           => 'required|string|max:255',
                'bank_account_number' => 'required|string|max:255',
                'bank_account_holder' => 'required|string|max:255',
                'pic_name'            => 'required|string|max:255',
                'pic_email'           => 'required|email|max:255|unique:vendors,pic_email|unique:users,email',
                'pic_phone'           => 'required|string|max:50',
            ]);
        }
    }
    protected array $messages = [
        'pic_email.unique' => 'Email ini sudah terdaftar dalam sistem (sebagai vendor lain atau akun user). Gunakan email lain.',
    ];
    public function submit()
    {
        $this->validate([
            'company_name'        => 'required|string|max:255',
            'address'             => 'required|string',
            'nib'                 => 'required|string|max:100|unique:vendors,nib',
            'npwp'                => 'required|string|max:100|unique:vendors,npwp',
            'has_halal'           => 'boolean',
            'has_haccp'           => 'boolean',
            'skp_number'          => 'nullable|string|max:255',
            'bank_name'           => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_account_holder' => 'required|string|max:255',
            'pic_name'            => 'required|string|max:255',
            'pic_email'           => 'required|email|max:255|unique:vendors,pic_email|unique:users,email',
            'pic_phone'           => 'required|string|max:50',
            'nib_file'            => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'npwp_file'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'certificate_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);


        $nibFileName = $this->nib_file->hashName();
        $npwpFileName = $this->npwp_file->hashName();
        $certFileName = $this->certificate_file ? $this->certificate_file->hashName() : null;

        $this->nib_file->storeAs('vendor-documents', $nibFileName, 'public');
        $this->npwp_file->storeAs('vendor-documents', $npwpFileName, 'public');

        if ($this->certificate_file) {
            $this->certificate_file->storeAs('vendor-documents', $certFileName, 'public');
        }

        Vendor::create([
            'company_name'        => $this->company_name,
            'address'             => $this->address,
            'nib'                 => $this->nib,
            'npwp'                => $this->npwp,
            'has_halal'           => $this->has_halal,
            'has_haccp'           => $this->has_haccp,
            'skp_number'          => $this->skp_number,
            'bank_name'           => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_holder' => $this->bank_account_holder,
            'pic_name'            => $this->pic_name,
            'pic_email'           => $this->pic_email,
            'pic_phone'           => $this->pic_phone,
            'nib_file'            => $nibFileName,
            'npwp_file'           => $npwpFileName,
            'certificate_file'    => $certFileName,
            'status'              => 'pending',
        ]);

        return redirect()->route('vendor.success')->with('message', 'Registrasi berhasil! Berkas Anda sedang direview oleh admin.');
    }
}
