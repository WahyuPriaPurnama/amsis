<?php

namespace App\Livewire\Purchasing\Vendor;

use App\Mail\VendorApprovedMail;
use App\Mail\VendorRejectedMail;
use App\Models\Purchasing\Vendor;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class VendorReview extends Component
{
    use AuthorizesRequests, WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?Vendor $selectedVendor = null;
    public string $rejection_reason = '';
    public string $actionType = ''; 
    public bool $isModalOpen = false;

    public function render()
    {
      
        return view('livewire.purchasing.vendor.vendor-review', [
            'vendors' => Vendor::where('status', 'pending')->latest()->paginate(10),
        ]);
    }

    public function openReviewModal(int $vendorId): void
    {
        $this->selectedVendor = Vendor::findOrFail($vendorId);
        $this->isModalOpen = true;
        $this->actionType = '';
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->selectedVendor = null;
        $this->rejection_reason = '';
        $this->actionType = '';
        $this->resetValidation();
    }

    public function approveVendor()
    {
        if (!$this->selectedVendor) {
            return;
        }

        $plainPassword = 'v-' . rand(1000, 9999);
        $vendor = $this->selectedVendor;

        DB::transaction(function () use ($vendor, $plainPassword) {
            $user = User::firstOrCreate(
                ['email' => $vendor->pic_email],
                [
                    'name'     => $vendor->pic_name,
                    'password' => Hash::make($plainPassword),
                ]
            );

            $vendor->update([
                'user_id' => $user->id,
                'status'  => 'approved',
            ]);

            Mail::to($vendor->pic_email)->send(new VendorApprovedMail($vendor, $user, $plainPassword));
        });

        session()->flash('message', "Vendor {$vendor->company_name} berhasil disetujui dan akun telah dibuat.");
        $this->closeModal();
    }

    public function rejectVendor()
    {
        $this->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        if (!$this->selectedVendor) {
            return;
        }

        $vendor = $this->selectedVendor;

        $vendor->update([
            'status'           => 'rejected',
            'rejection_reason' => $this->rejection_reason,
        ]);

        Mail::to($vendor->pic_email)->send(new VendorRejectedMail($vendor));

        session()->flash('message', "Vendor {$vendor->company_name} telah ditolak.");
        $this->closeModal();
    }
}
