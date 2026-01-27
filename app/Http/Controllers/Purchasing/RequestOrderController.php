<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\RequestOrder;
use App\Models\HRD\Subsidiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            $query = RequestOrder::with(['items', 'requester', 'subsidiary'])
                ->latest();

            // 🔑 Filter subsidiary berdasarkan role
            if (!$user->hasAnyRole(['super-admin', 'holding-admin'])) {
                // Ambil semua subsidiary dari role yang dimiliki user
                $subsidiaryIds = $user->roles
                    ->flatMap(fn($role) => $role->subsidiaries->pluck('id'))
                    ->unique();

                if ($subsidiaryIds->isNotEmpty()) {
                    $query->whereIn('subsidiary_id', $subsidiaryIds);
                } else {
                    abort(403, 'Anda tidak memiliki akses ke subsidiary manapun.');
                }
            }


            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_number', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhereHas('subsidiary', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items', function ($item) use ($search) {
                            $item->where('item_name', 'like', "%{$search}%");
                        });
                });
            }

            $orders = $query->paginate(20)->appends(['search' => $search]);

            return view('purchasing.request_order.index', compact('orders', 'search'));
        }

        abort(403, 'Anda tidak memiliki izin untuk melihat Request Order.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Full access role → semua subsidiaries
        if ($user->hasAnyRole(['super-admin', 'holding-admin'])) {
            $subsidiaries = Subsidiary::all();
        } else {
            // Ambil semua subsidiaries dari role yang dimiliki user
            $subsidiaries = $user->roles
                ->flatMap(fn($role) => $role->subsidiaries)
                ->unique('id');

            // Kalau employee/div-head → fallback ke subsidiary_id miliknya
            if ($subsidiaries->isEmpty() && $user->subsidiary_id) {
                $subsidiaries = Subsidiary::where('id', $user->subsidiary_id)->get();
            }

            if ($subsidiaries->isEmpty()) {
                abort(403, 'Role tidak dikenali atau tidak punya subsidiary.');
            }
        }

        return view('purchasing.request_order.create', compact('subsidiaries'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'subsidiary_id'      => 'required|exists:subsidiaries,id',
            'division'           => 'required|string|max:100',
            'request_date'       => 'required|date',
            'request_number'     => [
                'required',
                'string',
                Rule::unique('request_orders')->where(
                    fn($q) => $q->where('subsidiary_id', $request->subsidiary_id)
                ),
            ],
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
        ], [
            'request_number.unique' => 'Nomor RO sudah digunakan di plant ini.',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {

                // 1. Handle File Attachment Utama (Header)
                $attachmentPath = null;
                if ($request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')->store('attachments/ro', 'public');
                }

                // 2. Simpan Data Header (Request Order)
                $ro = RequestOrder::create([
                    'subsidiary_id'  => $validated['subsidiary_id'],
                    'division'       => $validated['division'],
                    'request_date'   => $validated['request_date'],
                    'request_number' => $validated['request_number'],
                    'purpose'        => $validated['purpose'] ?? null,
                    'attachment'     => $attachmentPath,
                    'status'         => 'pending',
                    'requested_by'   => Auth::id(),
                ]);

                // 3. Handle Items & File Attachment per Item
                foreach ($validated['items'] as $index => $itemData) {
                    $itemFilePath = null;

                    // Cek apakah ada file yang diupload pada index item ini
                    if ($request->hasFile("items.$index.receipt_attachment")) {
                        $itemFilePath = $request->file("items.$index.receipt_attachment")
                            ->store('attachments/ro_items', 'public');
                    }

                    // Masukkan path file ke dalam array data sebelum disimpan
                    $itemData['receipt_attachment'] = $itemFilePath;

                    // Simpan item satu per satu
                    $ro->items()->create($itemData);
                }

                return redirect()
                    ->route('request-order.index')
                    ->with('success', 'Request Order berhasil dibuat.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestOrder $requestOrder)
    {
        if (!Auth::user()->can('request-order.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:request_order_items,id',
            'items.*.date_received' => 'required|date', // Wajib diisi saat barang datang
            'items.*.qty_received'  => 'required|integer|min:1', // Minimal terima 1
            'items.*.po_date'       => 'nullable|date',
            'items.*.po_number'     => 'nullable|string|max:100',
            'items.*.receipt_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            return DB::transaction(function () use ($request, $requestOrder, $validated) {

                foreach ($validated['items'] as $index => $itemData) {
                    $item = $requestOrder->items()->findOrFail($itemData['id']);

                    // VALIDASI: Cek agar qty yang diterima tidak melebihi qty permintaan (quantity)
                    // Jika sistem kamu mengizinkan penerimaan bertahap, logika ini perlu disesuaikan
                    if ($itemData['qty_received'] > $item->quantity) {
                        throw new \Exception("Jumlah diterima untuk barang [{$item->item_name}] melebihi jumlah permintaan.");
                    }

                    $updateData = collect($itemData)->except(['receipt_attachment'])->toArray();

                    // Logic File Upload
                    if ($request->hasFile("items.$index.receipt_attachment")) {
                        if ($item->receipt_attachment && Storage::disk('public')->exists($item->receipt_attachment)) {
                            Storage::disk('public')->delete($item->receipt_attachment);
                        }
                        $path = $request->file("items.$index.receipt_attachment")->store('attachments/receipts', 'public');
                        $updateData['receipt_attachment'] = $path;
                    }

                    $item->update($updateData);
                }

                // OTOMATISASI STATUS:
                // Cek apakah semua item sudah diterima penuh
                $isAllReceived = $requestOrder->items->every(function ($item) {
                    return $item->qty_received >= $item->quantity;
                });

                if ($isAllReceived) {
                    $requestOrder->update(['status' => 'completed']);
                } else {
                    $requestOrder->update(['status' => 'partial']);
                }

                return redirect()->route('request-order.index')->with('success', 'Realisasi penerimaan barang berhasil disimpan.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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

                if ($totalReceived >= $totalRequested) {
                    $requestOrder->update(['status' => 'completed']);
                } elseif ($totalReceived > 0) {
                    $requestOrder->update(['status' => 'partial']);
                }
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
}
