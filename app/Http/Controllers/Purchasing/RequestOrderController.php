<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\RequestOrder;
use App\Models\HRD\Subsidiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequestOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->can('request-order.list')) {
            $query = RequestOrder::with(['items', 'requester', 'subsidiary'])->latest();

            // 1. Ambil daftar subsidiary untuk pilihan di dropdown filter
            // Jika bukan super-admin, hanya ambil subsidiary yang diizinkan untuk user tersebut
            if ($user->hasAnyRole(['super-admin', 'holding-admin'])) {
                $allSubsidiaries = \App\Models\HRD\Subsidiary::all();
            } else {
                $subsidiaryIds = $user->roles->flatMap(fn($role) => $role->subsidiaries->pluck('id'))->unique();
                $allSubsidiaries = \App\Models\HRD\Subsidiary::whereIn('id', $subsidiaryIds)->get();
                $query->whereIn('subsidiary_id', $subsidiaryIds);
            }

            // 2. Filter berdasarkan Dropdown Plant (Subsidiary)
            if ($subsidiaryId = $request->input('subsidiary_id')) {
                $query->where('subsidiary_id', $subsidiaryId);
            }

            // 3. Filter Search (Tetap ada)
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_number', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhereHas('items', function ($item) use ($search) {
                            $item->where('item_name', 'like', "%{$search}%");
                        });
                });
            }

            $orders = $query->paginate(20)->appends($request->all()); // Simpan semua parameter filter di link paginasi

            return view('purchasing.request_order.index', compact('orders', 'search', 'allSubsidiaries'));
        }

        abort(403);
    }
    /**
     * Show the form for creating a new resource.
     */

    private function generateRequestNumber($subsidiaryId, $date = null)
    {
        if (!$subsidiaryId) {
            return '';
        }

        $targetDate = $date ? strtotime($date) : time();
        $year = date('Y', $targetDate);
        $yearMonth = date('Ym', $targetDate);

        // Hitung transaksi aktif khusus subsidiary ini di TAHUN berjalan
        $nextSequence = RequestOrder::where('subsidiary_id', $subsidiaryId)
            ->whereYear('request_date', $year)
            ->count() + 1;

        // Format: YYYYMM/0001
        return sprintf('%s/%04d', $yearMonth, $nextSequence);
    }

    /**
     * Endpoint AJAX untuk form Alpine.js
     */
    public function getNextRequestNumber(Request $request)
    {
        $subsidiaryId = $request->input('subsidiary_id');
        $date = $request->input('request_date');

        if (!$subsidiaryId) {
            return response()->json(['request_number' => '']);
        }

        $requestNumber = $this->generateRequestNumber($subsidiaryId, $date);

        return response()->json(['request_number' => $requestNumber]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['super-admin', 'holding-admin'])) {
            $subsidiaries = Subsidiary::all();
        } else {
            $subsidiaries = $user->roles
                ->flatMap(fn($role) => $role->subsidiaries)
                ->unique('id');

            if ($subsidiaries->isEmpty() && $user->subsidiary_id) {
                $subsidiaries = Subsidiary::where('id', $user->subsidiary_id)->get();
            }

            if ($subsidiaries->isEmpty()) {
                abort(403, 'Role tidak dikenali atau tidak punya subsidiary.');
            }
        }

        // Nomor otomatis default dari subsidiary pertama & tanggal hari ini
        $defaultSubsidiaryId = $subsidiaries->first()->id ?? null;
        $autoRequestNumber = $this->generateRequestNumber($defaultSubsidiaryId, date('Y-m-d'));

        return view('purchasing.request_order.create', compact('subsidiaries', 'autoRequestNumber'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'subsidiary_id'      => 'required|exists:subsidiaries,id',
            'division'           => 'required|string|max:100',
            'request_date'       => 'required|date',
            'request_number'     => 'nullable|string|max:50',
            'purpose'            => 'nullable|string|max:500',
            'attachment'         => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'items'              => 'required|array|min:1',
            'items.*.item_name'  => 'required|string|max:255|distinct',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit'       => 'required|string|max:50',
            'items.*.remark'     => 'nullable|string|max:255',
            'items.*.date_received' => 'nullable|date',
            'items.*.qty_received'  => 'nullable|integer|min:0',
            'items.*.receipt_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'items.*.po_date'       => 'nullable|date',
            'items.*.po_number'     => 'nullable|string|max:100',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Generate nomor terbaru secara transaksional di server
                $requestNumber = $this->generateRequestNumber($validated['subsidiary_id'], $validated['request_date']);

                $attachmentPath = null;
                if ($request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')->store('attachments/ro', 'public');
                }

                $ro = RequestOrder::create([
                    'subsidiary_id'  => $validated['subsidiary_id'],
                    'division'       => $validated['division'],
                    'request_date'   => $validated['request_date'],
                    'request_number' => $requestNumber,
                    'purpose'        => $validated['purpose'] ?? null,
                    'attachment'     => $attachmentPath,
                    'status'         => 'pending',
                    'requested_by'   => Auth::id(),
                ]);

                foreach ($validated['items'] as $index => $itemData) {
                    $itemFilePath = null;
                    if ($request->hasFile("items.$index.receipt_attachment")) {
                        $itemFilePath = $request->file("items.$index.receipt_attachment")
                            ->store('attachments/ro_items', 'public');
                    }
                    $itemData['receipt_attachment'] = $itemFilePath;
                    $ro->items()->create($itemData);
                }

                return redirect()
                    ->route('request-order.index')
                    ->with('success', 'Request Order berhasil dibuat dengan nomor ' . $requestNumber);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $order = RequestOrder::with(['items', 'requester', 'subsidiary', 'divHead', 'plantManager'])->findOrFail($id);

        return view('purchasing.request_order.show', compact('order'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestOrder $requestOrder)
    {
        if (!Auth::user()->can('request-order.edit')) {
            abort(403);
        }

        $order = RequestOrder::with('items')->findOrFail($requestOrder->id);
        $subsidiaries = Subsidiary::all();
        return view('purchasing.request_order.edit', compact('order', 'subsidiaries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestOrder $requestOrder)
    {
        // Cek hak akses
        if (!Auth::user()->can('request-order.edit')) {
            abort(403);
        }

        // 1. VALIDASI DATA DARI FORM EDIT
        $validated = $request->validate([
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'division'      => 'required|string|max:255',
            'request_date'  => 'required|date',
            'purpose'       => 'required|string',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            'items'             => 'required|array|min:1',
            'items.*.id'        => 'nullable|exists:request_order_items,id', // Nullable karena barang baru tidak punya ID
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit'      => 'required|string|max:50',
            'items.*.remark'    => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request, $requestOrder, $validated) {

                // 2. UPDATE LAMPIRAN HEADER (Jika ada file baru)
                $attachmentPath = $requestOrder->attachment;
                if ($request->hasFile('attachment')) {
                    // Hapus file lama di storage jika ada
                    if ($requestOrder->attachment && Storage::disk('public')->exists($requestOrder->attachment)) {
                        Storage::disk('public')->delete($requestOrder->attachment);
                    }
                    $attachmentPath = $request->file('attachment')->store('attachments/ro', 'public');
                }

                // 3. UPDATE DATA INDUK REQUEST ORDER
                $requestOrder->update([
                    'subsidiary_id' => $validated['subsidiary_id'],
                    'division'      => $validated['division'],
                    'request_date'  => $validated['request_date'],
                    'purpose'       => $validated['purpose'],
                    'attachment'    => $attachmentPath,
                    'revision_count'  => $requestOrder->revision_count + 1, // Tambah hitungan revisi
                    'last_revised_at' => now(),
                ]);

                // 4. SINKRONISASI DAFTAR BARANG
                // Ambil semua ID barang yang dikirim dari form (yang nilainya tidak null)
                $submittedItemIds = collect($validated['items'])
                    ->pluck('id')
                    ->filter()
                    ->toArray();

                // Hapus barang di database yang ID-nya TIDAK ADA di form 
                // (Artinya user menekan tombol 'Trash' / hapus baris di form edit)
                $requestOrder->items()->whereNotIn('id', $submittedItemIds)->delete();

                // Looping data barang dari form untuk Update atau Create
                foreach ($validated['items'] as $itemData) {
                    if (!empty($itemData['id'])) {
                        // Update barang yang sudah ada
                        $requestOrder->items()->where('id', $itemData['id'])->update([
                            'item_name' => $itemData['item_name'],
                            'quantity'  => $itemData['quantity'],
                            'unit'      => $itemData['unit'],
                            'remark'    => $itemData['remark'],
                        ]);
                    } else {
                        // Create barang baru (jika user menekan tombol 'Tambah Baris Barang')
                        $requestOrder->items()->create([
                            'item_name' => $itemData['item_name'],
                            'quantity'  => $itemData['quantity'],
                            'unit'      => $itemData['unit'],
                            'remark'    => $itemData['remark'],
                        ]);
                    }
                }

                // Arahkan kembali ke halaman Detail setelah berhasil save
                return redirect()->route('request-order.show', $requestOrder->id)
                    ->with('success', 'Request Order berhasil diperbarui.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }


    public function receive(RequestOrder $requestOrder)
    {
        if (!Auth::user()->can('request-order.receive')) {
            abort(403);
        }

        $requestOrder = RequestOrder::with('items')->findOrFail($requestOrder->id);

        return view('purchasing.request_order.receive', compact('requestOrder'));
    }

    public function updateReceive(Request $request, RequestOrder $requestOrder)
    {
        if (!Auth::user()->can('request-order.receive')) {
            abort(403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:request_order_items,id',
            'items.*.qty_received' => 'nullable|integer|min:0', // Izinkan 0 jika salah input
            'items.*.date_received' => 'required_with:items.*.qty_received|nullable|date',
            'items.*.po_date' => 'nullable|date',
            'items.*.po_number' => 'nullable|string|max:100',
            'items.*.receipt_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $requestOrder, $validated) {
                foreach ($validated['items'] as $index => $itemData) {
                    // Lewati jika qty_received tidak diisi sama sekali (null)
                    if ($itemData['qty_received'] === null) {
                        continue;
                    }

                    $item = $requestOrder->items()->findOrFail($itemData['id']);

                    // Gunakan filter manual agar angka 0 tidak ikut terhapus
                    $updateData = collect($itemData)
                        ->except(['receipt_attachment'])
                        ->filter(fn($value) => !is_null($value))
                        ->toArray();

                    // Logika File Upload
                    if ($request->hasFile("items.$index.receipt_attachment")) {
                        if ($item->receipt_attachment && Storage::disk('public')->exists($item->receipt_attachment)) {
                            Storage::disk('public')->delete($item->receipt_attachment);
                        }
                        $path = $request->file("items.$index.receipt_attachment")->store('attachments/receipts', 'public');
                        $updateData['receipt_attachment'] = $path;
                    }

                    $item->update($updateData);
                }

                // --- LOGIKA UPDATE STATUS REQUEST ORDER ---
                $requestOrder->refresh(); // Ambil data terbaru setelah update items

                $totalRequested = $requestOrder->items->sum('quantity');
                $totalReceived = $requestOrder->items->sum('qty_received');

                // if ($totalReceived >= $totalRequested) {
                //     $requestOrder->update(['status' => 'completed']);
                // } elseif ($totalReceived > 0) {
                //     $requestOrder->update(['status' => 'partial']);
                // }
            });

            return redirect()->route('request-order.show', $requestOrder->id)
                ->with('success', 'Realisasi penerimaan barang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestOrder $requestOrder)
    {

        $requestOrder->items()->delete();

        $requestOrder->delete();

        return redirect()->route('request-order.index')
            ->with('success', 'Request Order berhasil dihapus.');
    }

    public function approveDivHead(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        if (!auth()->user()->can('request-order.approve-division') && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('request-order.index')
                ->with('error', 'Hanya Kepala Divisi yang berhak melakukan approve.');
        }

        $requestOrder->update([
            'status' => 'approved_by_div_head',
            'approved_by_div_head' => Auth::id(),
            'approved_by_divhead_at' => now(),
        ]);

        $message = 'Request Order telah disetujui oleh Kepala Divisi.';
        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('success', $message);
        }

        return redirect()->route('request-order.index')
            ->with('success', $message);
    }

    public function approveManager(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);

        if (!auth()->user()->can('request-order.approve-manager') && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('request-order.index')
                ->with('error', 'Hanya Plant Manager yang berhak melakukan approve.');
        }

        $requestOrder->update([
            'status'                 => 'approved_by_manager',
            'approved_by_manager'    => Auth::id(),
            'approved_by_manager_at' => now(),
        ]);

        $message = 'Request Order telah disetujui Plant Manager.';

        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('success', $message);
        }

        return redirect()->route('request-order.index')
            ->with('success', $message);
    }

    public function approveBod(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);

        if (!auth()->user()->can('request-order.approve-bod') && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('request-order.index')
                ->with('error', 'Hanya BOD yang berhak melakukan approve.');
        }

        $requestOrder->update([
            'status' => 'approved_by_bod',
            'approved_by_bod' => Auth::id(),
            'approved_by_bod_at' => now(),
        ]);

        $message = 'Request Order telah disetujui BOD.';
        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('success', $message);
        }
        return redirect()->route('request-order.index')
            ->with('success', $message);
    }
    public function pdf($id)
    {
        $order = RequestOrder::with(['items', 'requester', 'subsidiary', 'divHead', 'plantManager'])->findOrFail($id);
        $timestamp = now()->format('d/m/Y H:i:s');
        $pdf = Pdf::loadView('purchasing.request_order.pdf', compact('order', 'timestamp'));
        return $pdf->stream('Request_Order_' . $order->subsidiary->name . '_' . $order->request_number . '.pdf');
    }
    public function unapprove(Request $request, $id)
    {
        try {
            $requestOrder = RequestOrder::findOrFail($id);
            $user = auth()->user();

            switch ($requestOrder->status) {
                case 'approved_by_div_head':
                    if (!$user->can('request-order.approve-division') && !$user->can('request-order.unapprove') && !$user->hasRole('super-admin')) {
                        return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk membatalkan persetujuan ini.');
                    }
                    $requestOrder->status = 'pending';
                    $requestOrder->approved_by_div_head = null;
                    $requestOrder->approved_by_divhead_at = null;
                    break;

                case 'approved_by_manager':
                    if (!$user->can('request-order.approve-manager') && !$user->can('request-order.unapprove') && !$user->hasRole('super-admin')) {
                        return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk membatalkan persetujuan ini.');
                    }
                    $requestOrder->status = 'approved_by_div_head';
                    $requestOrder->approved_by_manager = null;
                    $requestOrder->approved_by_manager_at = null;
                    break;

                case 'approved_by_bod':
                    if (!$user->can('request-order.approve-bod') && !$user->can('request-order.unapprove') && !$user->hasRole('super-admin')) {
                        return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk membatalkan persetujuan ini.');
                    }
                    $requestOrder->status = 'approved_by_manager';
                    $requestOrder->approved_by_bod = null;
                    $requestOrder->approved_by_bod_at = null;
                    break;

                default:
                    return redirect()->back()->with('error', 'Status Request Order tidak dapat di-unapprove.');
            }

            $requestOrder->save();

            $message = 'Persetujuan Request Order berhasil dibatalkan.';

            if ($request->input('from') === 'show') {
                return redirect()->route('request-order.show', $id)->with('success', $message);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            // Log error asli ke storage/logs/laravel.log untuk mempermudah pengecekan
            \Illuminate\Support\Facades\Log::error('Error Unapprove RO: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal membatalkan persetujuan: ' . $e->getMessage());
        }
    }
}
