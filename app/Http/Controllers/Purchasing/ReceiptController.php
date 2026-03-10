<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\MasterSupplier;
use App\Models\Purchasing\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::with('supplier')->latest()->get();
        return view('purchasing.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $suppliers = MasterSupplier::all();
        return view('purchasing.receipts.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        // 1. Simpan Data Kedatangan
        $receipt = Receipt::create([
            'supplier_id'    => $request->supplier_id,
            'reference_number' => 'GR-' . now()->format('YmdHis'),
            'arrival_date'   => $request->arrival_date,
            'received_by'    => auth()->user()->name,
        ]);

        // 2. Loop Dokumen yang diupload (Misal inputnya array: documents[PO], documents[SURAT_JALAN])
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $type => $file) {
                $path = $file->store("receipts/{$receipt->id}", 'public');

                $receipt->documents()->create([
                    'type' => strtoupper($type),
                    'document_number' => $request->doc_numbers[$type], // Ambil nomor dokumen sesuai tipenya
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('receipts.index')->with('success', 'Data kedatangan berhasil dicatat!');
    }
}
