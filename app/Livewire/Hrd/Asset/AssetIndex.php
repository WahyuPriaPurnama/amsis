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
        $query = Asset::with(['subsidiary', 'user'])
            ->when($this->subsidiary_id, function ($q) {
                $q->where('subsidiary_id', $this->subsidiary_id);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            });

        $totalItemsCount = (clone $query)->count();

        // Mengelompokkan dan menghitung jumlah aset berdasarkan nama barangnya secara spesifik
        $assetBreakdown = (clone $query)
            ->select('name', \DB::raw('count(*) as total'))
            ->groupBy('name')
            ->orderBy('total', 'desc')
            ->get();

        $assets = (clone $query)->latest()->paginate(10);

        return view('livewire.hrd.asset.asset-index', [
            'assets'           => $assets,
            'allSubsidiaries'  => Subsidiary::orderBy('name', 'asc')->get(),
            'totalItemsCount'  => $totalItemsCount,
            'assetBreakdown'   => $assetBreakdown, // Kirim data rincian ke view
        ]);
    }
}
