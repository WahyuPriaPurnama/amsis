<?php
namespace App\Livewire\Purchasing\Vendor;

use Livewire\Component;
use App\Models\Purchasing\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// Jika menggunakan mail, import class Mail dan Mailable Anda di sini

class VendorReview extends Component
{
    public $vendors;
    public $selectedVendor = null;
    public $rejection_reason;
    public $isModalOpen = false;
    public $actionType = ''; // 'approve' atau 'reject'

    public function render()
    {
        // Ambil data vendor yang statusnya masih pending
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

    // Aksi Menyetujui Vendor (YA)
    public function approveVendor($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        // 1. Buat akun user baru untuk vendor berdasarkan email PIC
        $user = User::create([
            'name' => $vendor->pic_name,
            'email' => $vendor->pic_email,
            'password' => Hash::make(Str::random(12)), // Password sementara
            // Tambahkan role vendor jika menggunakan Spatie Permission, misal: $user->assignRole('vendor');
        ]);

        // 2. Update status vendor menjadi approved dan kaitkan user_id-nya
        $vendor->update([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        // 3. TODO: Kirim Email Aktivasi & Set Password ke $vendor->pic_email
        // Mail::to($vendor->pic_email)->send(new VendorApprovedMail($vendor, $user));

        session()->flash('message', "Vendor {$vendor->company_name} berhasil disetujui dan akun telah dibuat.");
        $this->closeModal();
    }

    // Aksi Menolak Vendor (TIDAK)
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

        // TODO: Kirim Email Penolakan + Alasan Perbaikan Berkas ke $vendor->pic_email
        // Mail::to($vendor->pic_email)->send(new VendorRejectedMail($vendor));

        session()->flash('message', "Vendor {$vendor->company_name} ditolak.");
        $this->closeModal();
    }
}