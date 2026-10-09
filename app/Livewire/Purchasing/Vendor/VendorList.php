<?php

namespace App\Livewire\Purchasing\Vendor;

use App\Models\Purchasing\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Daftar Vendor')]
class VendorList extends Component
{
    use AuthorizesRequests, WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    // Filter & Search Properties
    public string $statusFilter = 'all'; // Opsi: 'all', 'approved', 'inactive', 'blacklisted'
    public string $search = '';
    public bool $filterExpiringSoon = false;

    // Modal States
    public ?Vendor $detailVendor = null;
    public bool $isDetailModalOpen = false;

    // Form Kontrak Admin
    public ?Vendor $selectedVendorForContract = null;
    public bool $isContractModalOpen = false;
    public string $contract_number = '';
    public string $contract_start_date = '';
    public string $contract_end_date = '';
    public $contract_file;

    // Reset pagination saat filter/search berubah
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFilterExpiringSoon(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Vendor::query();

        // 1. Filter berdasarkan Dropdown Status Vendor
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        } else {
            // Tampilkan vendor yang sudah diproses (Approved, Inactive, dan Blacklisted)
            $query->whereIn('status', ['approved', 'inactive', 'blacklisted']);
        }

        // 2. Pencarian Nama Vendor, PIC, atau No Kontrak
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('pic_name', 'like', '%' . $this->search . '%')
                    ->orWhere('contract_number', 'like', '%' . $this->search . '%');
            });
        }

        // 3. Filter Kontrak Habis (<= 30 Hari)
        if ($this->filterExpiringSoon) {
            $query->expiringSoon(30);
        }

        return view('livewire.purchasing.vendor.vendor-list', [
            'vendors' => $query->latest()->paginate(10),
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                              STATUS ACTIONS                                */
    /* -------------------------------------------------------------------------- */

    /**
     * Mengubah status vendor secara dinamis ('approved', 'inactive', 'blacklisted').
     */
    public function setVendorStatus(int $vendorId, string $newStatus): void
    {
        if (!in_array($newStatus, ['approved', 'inactive', 'blacklisted'])) {
            return;
        }

        $vendor = Vendor::findOrFail($vendorId);
        $vendor->update(['status' => $newStatus]);

        $statusLabel = [
            'approved'    => 'diaktifkan kembali',
            'inactive'    => 'dinonaktifkan',
            'blacklisted' => 'dimasukkan ke daftar BLACKLIST',
        ][$newStatus];

        session()->flash('message', "Vendor {$vendor->company_name} berhasil {$statusLabel}.");
    }

    /* -------------------------------------------------------------------------- */
    /*                              MODAL FUNCTIONS                               */
    /* -------------------------------------------------------------------------- */

    public function showDetail(int $vendorId): void
    {
        $this->detailVendor = Vendor::findOrFail($vendorId);
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->detailVendor = null;
    }

    public function openContractModal(int $vendorId): void
    {
        $vendor = Vendor::findOrFail($vendorId);
        $this->selectedVendorForContract = $vendor;
        $this->contract_number = $vendor->contract_number ?? '';
        $this->contract_start_date = $vendor->contract_start_date?->format('Y-m-d') ?? '';
        $this->contract_end_date = $vendor->contract_end_date?->format('Y-m-d') ?? '';
        $this->isContractModalOpen = true;
    }

    public function closeContractModal(): void
    {
        $this->isContractModalOpen = false;
        $this->selectedVendorForContract = null;
        $this->reset(['contract_number', 'contract_start_date', 'contract_end_date', 'contract_file']);
        $this->resetValidation();
    }

    public function saveContract(): void
    {
        $this->validate([
            'contract_number'     => 'required|string|max:255',
            'contract_start_date' => 'required|date',
            'contract_end_date'   => 'required|date|after_or_equal:contract_start_date',
            'contract_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if (!$this->selectedVendorForContract) {
            return;
        }

        $data = [
            'contract_number'     => $this->contract_number,
            'contract_start_date' => $this->contract_start_date,
            'contract_end_date'   => $this->contract_end_date,
        ];

        if ($this->contract_file) {
            $contractFileName = $this->contract_file->hashName();
            $this->contract_file->storeAs('vendor-contracts', $contractFileName, 'public');
            $data['contract_file'] = $contractFileName;
        }

        $this->selectedVendorForContract->update($data);

        session()->flash('message', 'Perjanjian kontrak vendor berhasil disimpan!');
        $this->closeContractModal();
    }
}
