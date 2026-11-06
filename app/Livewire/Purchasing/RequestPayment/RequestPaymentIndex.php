<?php

namespace App\Livewire\Purchasing\RequestPayment;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Request Payment')]
class RequestPaymentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $subsidiary_id = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSubsidiaryId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'subsidiary_id']);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Inisialisasi Query dasar
        $query = RequestPayment::with(['subsidiary', 'items']);

        // 2. Tentukan daftar ID Subsidiary yang diizinkan untuk User / Role yang login
        if (!$user->hasRole('super-admin')) {
            // Ambil daftar Subsidiary ID yang terikat pada Role user ini
            $allowedSubsidiaryIds = $user->roles()
                ->with('subsidiaries')
                ->get()
                ->pluck('subsidiaries')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->toArray();

            // Saring data RFP yang tampil di tabel
            $query->whereIn('subsidiary_id', $allowedSubsidiaryIds);
        } else {
            $allowedSubsidiaryIds = null;
        }

        // 3. Filter berdasarkan opsi dropdown Plant jika user memilihnya
        if ($this->subsidiary_id) {
            $query->where('subsidiary_id', $this->subsidiary_id);
        }

        // 4. Pencarian reaktif
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('payment_number', 'like', '%' . $this->search . '%')
                    ->orWhere('division', 'like', '%' . $this->search . '%')
                    ->orWhereHas('items', function ($itemQuery) {
                        $itemQuery->where('item_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // 5. Saring isi Dropdown Plant (Hanya tampilkan Plant/Subsidiary yang diizinkan)
        $allSubsidiaries = $user->hasRole('super-admin')
            ? Subsidiary::orderBy('name', 'asc')->get()
            : Subsidiary::whereIn('id', $allowedSubsidiaryIds)->orderBy('name', 'asc')->get();

        return view('purchasing.request-payment.request-payment-index', [
            'payments'        => $query->latest()->paginate(10),
            'allSubsidiaries' => $allSubsidiaries, // Opsi dropdown otomatis hanya RMM untuk user non super-admin
        ]);
    }
}
