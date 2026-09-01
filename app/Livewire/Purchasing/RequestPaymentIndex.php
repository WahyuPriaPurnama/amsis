<?php

namespace App\Livewire\Purchasing;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
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
        $payments = RequestPayment::with(['subsidiary', 'items'])
            ->when($this->subsidiary_id, fn($query) => $query->where('subsidiary_id', $this->subsidiary_id))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_number', 'like', '%' . $this->search . '%')
                        ->orWhere('division', 'like', '%' . $this->search . '%')
                        ->orWhereHas('items', function ($itemQuery) {
                            $itemQuery->where('item_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('purchasing.request-payment.index', [
            'payments'        => $payments,
            'allSubsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
