<?php

namespace App\Livewire\Purchasing\Vendor;

use App\Models\Purchasing\Vendor;
use Livewire\Component;

class VendorPortal extends Component
{
    public function render()
    {
        $vendor=Vendor::where('user_id', auth()->id())->first();
        return view('livewire.purchasing.vendor.vendor-portal', compact('vendor'));
    }
}
