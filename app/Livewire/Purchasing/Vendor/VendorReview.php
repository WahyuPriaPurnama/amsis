<?php

namespace App\Livewire\Purchasing\Vendor;

use Livewire\Component;
use App\Models\Purchasing\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VendorApprovedMail;
use App\Mail\VendorRejectedMail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorReview extends Component
{
    public $vendors;
    public $selectedVendor = null;
    public $rejection_reason;
    public $isModalOpen = false;
    public $actionType = ''; 
    use AuthorizesRequests;
    public function render()
    {
  
        $this->vendors = Vendor::where('status', 'pending')->latest()->get();
        return view('livewire.purchasing.vendor.vendor-review');
    }

    // Membuka modal detail/review vendor
    public function openReviewModal($vendorId)
    {
        $this->selectedVendor = Vendor::findOrFail($vendorId);
        $this->isModalOpen = true;
    }

    // Menutup modal
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedVendor = null;
        $this->rejection_reason = '';
    }

    public function approveVendor()
    {
        if (!$this->selectedVendor) {
            return;
        }

        $vendor = Vendor::findOrFail($this->selectedVendor->id);

        $plainPassword = 'v-' . rand(1000, 9999);

        $user = User::create([
            'name' => $vendor->pic_name,
            'email' => $vendor->pic_email,
            'password' => Hash::make($plainPassword),
        ]);

        $vendor->update([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        Mail::to($vendor->pic_email)->send(new VendorApprovedMail($vendor, $user, $plainPassword));

        session()->flash('message', "Vendor {$vendor->company_name} berhasil disetujui dan akun telah dibuat.");
        $this->closeModal();
    }

    public function rejectVendor()
    {
        $this->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $vendor = Vendor::findOrFail($this->selectedVendor->id);

        $vendor->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejection_reason,
        ]);


        Mail::to($vendor->pic_email)->send(new VendorRejectedMail($vendor));

        session()->flash('message', "Vendor {$vendor->company_name} ditolak.");
        $this->closeModal();
    }
}
