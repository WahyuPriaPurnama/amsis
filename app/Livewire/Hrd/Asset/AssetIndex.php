<?php

namespace App\Livewire\Hrd\Asset;

use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Aset')]
class AssetIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $subsidiary_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubsidiaryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $assets = Asset::with(['subsidiary', 'user'])
            ->when($this->subsidiary_id, function ($query) {
                $query->where('subsidiary_id', $this->subsidiary_id);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.hrd.asset.asset-index', [
            'assets'          => $assets,
            'allSubsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
            'subsidiary_id'   => $this->subsidiary_id,
            'search'          => $this->search,
        ]);
    }
}
