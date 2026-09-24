<?php

namespace App\Livewire\Purchasing\Vendor;

use App\Models\Purchasing\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Daftar Vendor Disetujui')]
class VendorList extends Component
{
    use AuthorizesRequests;
    public function render()
    {

        $vendors = Vendor::where('status', 'approved')->latest()->get();
        return view('livewire.purchasing.vendor.vendor-list', compact('vendors'));
    }

    public $detailVendor = null;
    public $isDetailModalOpen = false;

    public function showDetail($vendorId)
    {
        $this->detailVendor = Vendor::findOrFail($vendorId);
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->detailVendor = null;
    }
}
