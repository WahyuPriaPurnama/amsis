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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubsidiaryId()
    {
        $this->resetPage();
    }

    // Tambahkan di dalam class AssetIndex.php
    public function exportPdf()
    {
        $user = auth()->user();
        $subsidiaryId = $this->subsidiary_id ?: $user->subsidiary_id;

        if ($user->hasRole(['super-admin', 'holding-admin'])) {
            if ($this->subsidiary_id) {
                $assets = Asset::with(['subsidiary', 'user'])->where('subsidiary_id', $this->subsidiary_id)->latest()->get();
                $subsidiary = Subsidiary::find($this->subsidiary_id);
            } else {
                $assets = Asset::with(['subsidiary', 'user'])->latest()->get();
                $subsidiary = null;
            }
        } else {
            $assets = Asset::with(['subsidiary', 'user'])->where('subsidiary_id', $subsidiaryId)->latest()->get();
            $subsidiary = Subsidiary::find($subsidiaryId);
        }

        $pdf = Pdf::loadView('livewire.hrd.asset.export-pdf', compact('assets', 'subsidiary'));
        return response()->streamDownload(fn() => print($pdf->output()), 'asset_list.pdf');
    }

    public function exportExcel()
    {
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
        $totalQuantityCount = (clone $query)->sum('quantity'); // <-- Ubah 'qty' sesuai nama kolom di database Anda

        // Mengelompokkan dan menghitung jumlah aset serta total qty berdasarkan nama barang
        $assetBreakdown = (clone $query)
            ->select('name', DB::raw('count(*) as total'), DB::raw('sum(quantity) as total_qty')) // <-- Tambahkan sum(quantity)
            ->groupBy('name')
            ->orderBy('total_qty', 'desc')
            ->get();

        $assets = (clone $query)->latest()->paginate(10);

        return view('livewire.hrd.asset.asset-index', [
            'assets'             => $assets,
            'allSubsidiaries'    => Subsidiary::orderBy('name', 'asc')->get(),
            'totalItemsCount'    => $totalItemsCount,
            'totalQuantityCount' => $totalQuantityCount, // <-- Kirim ke view
            'assetBreakdown'     => $assetBreakdown,
        ]);
    }
}
