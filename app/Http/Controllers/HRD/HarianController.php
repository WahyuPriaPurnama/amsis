<?php

namespace App\Http\Controllers\HRD;

use App\Exports\HarianExport;
use App\Http\Controllers\Controller;
use App\Imports\HarianImport;
use App\Models\HRD\Employee;
use App\Models\HRD\Harian;
use App\Models\HRD\Scanlog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HarianController extends Controller
{
    public function cetakSlip(Request $request, $pin)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end = $request->end_date;
        if (!Scanlog::where('pin', $pin)->whereBetween('tgl', [$start, $end])->exists()) {
            return redirect()->back()->with('error', 'Tidak ada data scanlog untuk PIN ' . $pin . ' pada rentang tanggal yang dipilih.');
        }
        $slips = Scanlog::with('harian')->whereHas('harian', function ($query) use ($pin) {
            $query->where('pin', $pin);
        })
            ->whereBetween('tgl', [$start, $end])
            ->get();
        $totalGaji = $slips->sum('tgaji');
        $pdf = Pdf::loadView('scanlog.slip', compact('slips', 'start', 'end', 'totalGaji'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream();
    }

    public function cetakSlipMasal(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $start = $request->start_date;
        $end = $request->end_date;
        $slips = Scanlog::with('harian')->whereBetween('tgl', [$start, $end])->get();
        $totalGaji = $slips->sum('tgaji');
        if ($slips->isEmpty()) {
            return response()->view('scanlog.slip_kosong', compact('start', 'end'));
        }
        $pdf = Pdf::loadView('scanlog.slip', compact('slips', 'start', 'end', 'totalGaji'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream();
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx'
        ]);

        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = $file->hashName();

        //temporary file
        $path = $file->storeAs('public/excel/', $nama_file);

        // import data
        $import = Excel::import(new HarianImport, storage_path('app/public/excel/' . $nama_file));

        //remove from server
        Storage::delete($path);

        if ($import) {
            //redirect
            return redirect()->route('karyawan-harian.index')->with(['success' => 'Data Berhasil Diimport!']);
        } else {
            //redirect
            return redirect()->route('karyawan-harian.index')->with(['error' => 'Data Gagal Diimport!']);
        }
    }

    public function export()
    {
        return Excel::download(new HarianExport(), "karyawan " . now() . ".xlsx");
    }

    public function truncate()
    {
        Harian::truncate();
        return redirect()->route('karyawan-harian.index')->with(['success' => 'Data Berhasil Dikosongkan!']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Employee::class);
        $harian = Harian::latest()->get();
        return view('hrd.scanlog.harian', compact('harian'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Harian $harian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Harian $harian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Harian $harian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Harian $harian)
    {
        //
    }
}
