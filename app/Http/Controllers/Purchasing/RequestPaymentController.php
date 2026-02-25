<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
use App\Models\Purchasing\RequestPaymentItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->can('request-payment.list')) {
            $query = RequestPayment::with(['items', 'requester', 'subsidiary'])
                ->latest();

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


            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_number', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhereHas('items', function ($item) use ($search) {
                            $item->where('item_name', 'like', "%{$search}%");
                        });
                });
            }
            $payments = $query->paginate(20)->appends(['search' => $search]);

            return view('purchasing.request_payment.index', compact('payments', 'search', 'allSubsidiaries'));
        }

        abort(403, 'Anda tidak memiliki izin untuk melihat Request Payment.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->can('request-payment.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Request Payment.');
        }

        if ($user->hasAnyRole(['super-admin', 'holding-admin'])) {

            $subsidiaries = Subsidiary::orderBy('name')->get();
        } else {

            $subsidiaries = $user->roles
                ->flatMap(fn($role) => $role->subsidiaries)
                ->unique('id');


            if ($subsidiaries->isEmpty() && $user->subsidiary_id) {
                $subsidiaries = Subsidiary::where('id', $user->subsidiary_id)->get();
            }

            if ($subsidiaries->isEmpty()) {
                abort(403, 'Anda tidak memiliki akses ke subsidiary manapun.');
            }
        }

        return view('purchasing.request_payment.create', compact('subsidiaries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'payment_number'    => 'required|string|max:50',
            'date'              => 'required|date',
            'division'          => 'required|string|max:100',
            'purpose'           => 'nullable|string|max:255',
            'subsidiary_id'     => 'required|exists:subsidiaries,id',
            'attachment'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.unit'      => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.due_date'    => 'nullable|date',
        ]);

        $grandTotal = collect($validated['items'])->reduce(function ($carry, $item) {
            return $carry + ($item['quantity'] * $item['unit_price']);
        }, 0);

        // Simpan header Request Payment
        $payment = new RequestPayment();
        $payment->payment_number = $validated['payment_number'];
        $payment->date           = $validated['date'];
        $payment->division       = $validated['division'];
        $payment->purpose        = $validated['purpose'];
        $payment->subsidiary_id  = $validated['subsidiary_id'];
        $payment->requested_by   = Auth::id();
        $payment->grand_total    = $grandTotal;

        // Upload lampiran jika ada
        if ($request->hasFile('attachment')) {
            $payment->attachment = $request->file('attachment')
                ->store('attachments/request-payments', 'public');
        }

        $payment->save();

        // Simpan detail item
        foreach ($validated['items'] as $item) {
            RequestPaymentItem::create([
                'request_payment_id' => $payment->id,
                'item_name'          => $item['item_name'],
                'quantity'           => $item['quantity'],
                'unit'               => $item['unit'],
                'unit_price'         => $item['unit_price'],
                'amount'             => $item['quantity'] * $item['unit_price'],
                'due_date'             => $item['due_date'] ?? null,
            ]);
        }

        return redirect()->route('request-payment.index')
            ->with('success', 'Request Payment berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = RequestPayment::with(['items', 'requester', 'subsidiary', 'plantManager', 'bod'])->findOrFail($id); {
            return view('purchasing.request_payment.show', compact('payment'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestPayment $requestPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestPayment $requestPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestPayment $requestPayment)
    {
        $requestPayment->items()->delete();
        $requestPayment->delete();
        return redirect()->route('request-payment.index')
            ->with('success', 'Request Payment berhasil dihapus.');
    }

    public function approveManager(Request $request, $id)
    {
        $payment = RequestPayment::findOrFail($id);

        if (!auth()->user()->can('request-payment.approve-manager') && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('request-payment.index')
                ->with('error', 'Hanya Plant Manager yang berhak melakukan approve.');
        }

        $payment->update([
            'status'                 => 'approved_by_manager',
            'approved_by_manager'    => auth()->id(),
            'approved_by_manager_at' => now(),
        ]);
        $message = 'Request Order telah disetujui Plant Manager.';

        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('success', $message);
        }

        return redirect()->route('request-payment.index')
            ->with('success', $message);
    }
    public function approveBod($id)
    {
        $payment = RequestPayment::findOrFail($id);

        if (!auth()->user()->can('request-payment.approve-bod') && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('request-payment.index')
                ->with('error', 'Hanya BOD yang berhak melakukan approve.');
        }

        $payment->update([
            'status'              => 'approved_by_bod',
            'approved_by_bod'     => auth()->id(),
            'approved_by_bod_at'  => now(),
        ]);

        return redirect()->route('request-payment.index')
            ->with('success', 'Request Payment berhasil disetujui oleh BOD.');
    }

    public function pdf($id)
    {
        $payment = RequestPayment::with(['items', 'requester', 'subsidiary', 'plantManager', 'bod'])->findOrFail($id);
        $timestamp = now()->format('d/m/Y H:i:s');
        $pdf = Pdf::loadView('purchasing.request_payment.pdf', compact('payment', 'timestamp'));
        return $pdf->stream('Request_Payment_' . $payment->subsidiary->name . '_' . $payment->payment_number . '.pdf');
    }

    public function attachment($id)
    {
        $requestPayment = RequestPayment::findOrFail($id);

        if (!$requestPayment->attachment) {
            abort(404, 'Lampiran tidak tersedia');
        }

        $path = $requestPayment->attachment;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
