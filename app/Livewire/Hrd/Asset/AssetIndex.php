<?php

namespace App\Livewire\Hrd\Asset;

use App\Exports\AssetsExport;
use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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

    public function mount()
    {
        // Permission Check
        if (!auth()->user()->can('asset.list')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat daftar aset.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubsidiaryId()
    {
        $this->resetPage();
    }

    public function exportPdf()
    {
        if (!auth()->user()->can('asset.list')) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor data aset.');
        }

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

        $assets = $query->latest()->get();
        $subsidiary = $this->subsidiary_id ? Subsidiary::find($this->subsidiary_id) : null;

        $pdf = Pdf::loadView('livewire.hrd.asset.export-pdf', compact('assets', 'subsidiary'));
        return response()->streamDownload(fn() => print($pdf->output()), 'asset_list.pdf');
    }

    public function exportExcel()
    {
        if (!auth()->user()->can('asset.list')) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor data aset.');
        }

        return Excel::download(new AssetsExport($this->subsidiary_id, $this->search), 'asset_list.xlsx');
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

        // Hitung total baris item dan total keseluruhan Qty
        $totalItemsCount = (clone $query)->count();
        $totalQuantityCount = (clone $query)->sum('quantity');

        // Mengelompokkan dan menghitung jumlah aset serta total qty berdasarkan nama barang
        $assetBreakdown = (clone $query)
            ->select('name', DB::raw('count(*) as total'), DB::raw('sum(quantity) as total_qty'))
            ->groupBy('name')
            ->orderBy('total_qty', 'desc')
            ->get();

        $assets = (clone $query)->latest()->paginate(10);

        return view('livewire.hrd.asset.asset-index', [
            'assets'             => $assets,
            'allSubsidiaries'    => Subsidiary::orderBy('name', 'asc')->get(),
            'totalItemsCount'    => $totalItemsCount,
            'totalQuantityCount' => $totalQuantityCount,
            'assetBreakdown'     => $assetBreakdown,
        ]);
    }
}
