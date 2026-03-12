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
        // 1. Validasi Input
        $validated = $request->validate([
            'supplier_id'      => 'required|exists:master_supplier,id',
            'arrival_date'     => 'required|date',
            'notes'            => 'nullable|string|max:500',

            // Validasi array nomor dokumen
            'doc_numbers'      => 'required|array',
            'doc_numbers.*'    => 'nullable|string|max:100', // Nomor dokumen boleh kosong tapi maksimal 100 karakter

            // Validasi array file gambar
            'files'            => 'required|array|min:1', // Minimal harus ada 1 file yang diunggah
            'files.*'          => 'image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB per foto
        ], [
            // Pesan kustom jika diperlukan
            'files.required'   => 'Minimal satu foto dokumen fisik harus diunggah.',
            'files.*.image'    => 'File yang diunggah harus berupa gambar.',
            'supplier_id.required' => 'Silakan pilih supplier terlebih dahulu.',
        ]);

        try {
            // Gunakan DB Transaction agar jika upload file gagal, data Receipt tidak tersimpan (inkonsistensi data)
            return \DB::transaction(function () use ($request) {

                // 2. Simpan Data Master Kedatangan
                $receipt = Receipt::create([
                    'supplier_id'      => $request->supplier_id,
                    'reference_number' => now()->format('YmdHis'),
                    'arrival_date'     => $request->arrival_date,
                    'received_by'      => auth()->user()->name ?? 'System',
                    'notes'            => $request->notes,
                ]);

                // 3. Loop Dokumen & Upload
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $type => $file) {
                        // Simpan file ke storage/app/public/receipts/{id}
                        $path = $file->store("receipts/{$receipt->id}", 'public');

                        // Simpan ke tabel detail/dokumen
                        $receipt->documents()->create([
                            'type'            => strtoupper($type),
                            'document_number' => $request->doc_numbers[$type] ?? null,
                            'file_path'       => $path,
                        ]);
                    }
                }

                return redirect()->route('receipts.index')
                    ->with('success', 'Data kedatangan dan dokumen berhasil dicatat!');
            });
        } catch (\Exception $e) {
            // Jika gagal, hapus file yang mungkin sudah terlanjur terupload (opsional)
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $receipt = Receipt::with('supplier', 'documents')->findOrFail($id);
        return view('purchasing.receipts.show', compact('receipt'));
    }

    public function edit($id)
    {
        $receipt = Receipt::findOrFail($id);
        $suppliers = MasterSupplier::all();
        return view('purchasing.receipts.edit', compact('receipt', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'supplier_id'   => 'required|exists:master_supplier,id',
            'arrival_date'  => 'required|date',
            'notes'         => 'nullable|string|max:500',
            'doc_numbers'   => 'required|array',
            'doc_numbers.*' => 'nullable|string|max:100',
            'files'         => 'nullable|array',
            'files.*'       => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            return \DB::transaction(function () use ($request, $id) {
                $receipt = Receipt::findOrFail($id);

                // 2. Update Data Utama
                $receipt->update([
                    'supplier_id'  => $request->supplier_id,
                    'arrival_date' => $request->arrival_date,
                    'notes'        => $request->notes,
                ]);

                // 3. Sinkronisasi Dokumen
                $types = ['SURAT_JALAN', 'PO', 'FAKTUR'];

                foreach ($types as $type) {
                    $existingDoc = $receipt->documents()->where('type', $type)->first();
                    $newFile = $request->file("files.$type");

                    // Siapkan data yang akan di-update
                    $data = [
                        'document_number' => $request->doc_numbers[$type] ?? null,
                    ];

                    // Jika ada file baru yang diunggah
                    if ($newFile) {
                        // Hapus file lama jika ada sebelum menyimpan yang baru
                        if ($existingDoc && $existingDoc->file_path) {
                            \Storage::disk('public')->delete($existingDoc->file_path);
                        }

                        // Simpan file baru
                        $data['file_path'] = $newFile->store("receipts/{$receipt->id}", 'public');
                    }

                    // LOGIKA PENTING:
                    // Kita hanya melakukan update/create jika:
                    // 1. Dokumen sudah ada di DB (untuk update nomor/file), ATAU
                    // 2. Ada file baru yang diunggah (untuk buat dokumen baru)
                    if ($existingDoc || $newFile) {
                        $receipt->documents()->updateOrCreate(
                            ['type' => $type],
                            $data
                        );
                    }
                }

                return redirect()->route('receipts.index')
                    ->with('success', 'Kedatangan barang berhasil diperbarui!');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $receipt = Receipt::findOrFail($id);

            // 1. Hapus File secara fisik
            foreach ($receipt->documents as $document) {
                // Gunakan disk 'public' yang benar
                if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                    \Storage::disk('public')->delete($document->file_path);
                }
            }

            // 2. Opsi: Hapus folder induknya (misal: receipts/7) agar tidak menyisakan folder kosong
            $directory = "receipts/{$receipt->id}";
            if (\Storage::disk('public')->exists($directory)) {
                \Storage::disk('public')->deleteDirectory($directory);
            }

            // 3. Hapus data dari database
            // Jika Anda menggunakan Cascade Delete di database, documents akan otomatis terhapus.
            // Jika tidak, hapus manual: $receipt->documents()->delete();
            $receipt->delete();

            return redirect()->route('receipts.index')
                ->with('success', 'Data kedatangan dan file lampiran berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
