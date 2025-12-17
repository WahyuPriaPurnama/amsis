<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\RequestOrder;
use App\Models\HRD\Subsidiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RequestOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Pastikan user punya permission 'view request orders'
        if ($user->can('request-order.list')) {
            $orders = RequestOrder::with(['items', 'requester', 'subsidiary'])
                ->latest()
                ->paginate(20);

            return view('purchasing.request_order.index', compact('orders'));
        }

        // Jika tidak punya permission → abort
        abort(403, 'Anda tidak memiliki izin untuk melihat Request Order.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        $fullAccessRoles = ['super-admin', 'holding-admin'];
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        // Tentukan subsidiaries berdasarkan role
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($fullAccessRoles)) {
            $subsidiaries = Subsidiary::all();
        } elseif (method_exists($user, 'hasRole')) {
            $subsidiaries = collect();

            foreach ($roleSubsidiaryMap as $role => $id) {
                if ($user->hasRole($role)) {
                    $subsidiaries = Subsidiary::where('id', $id)->get();
                    break;
                }
            }

            if ($subsidiaries->isEmpty()) {
                abort(403, 'Role tidak dikenali');
            }
        } else {
            // Fallback jika trait belum aktif
            $subsidiaries = Subsidiary::where('id', 5)->get();
        }

        return view('purchasing.request_order.create', compact('subsidiaries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subsidiary_id'     => 'required|exists:subsidiaries,id',
            'division'          => 'required|string|max:100',
            'request_date'      => 'required|date',
            'request_number'    => [
                'required',
                'string',
                Rule::unique('request_orders')->where(function ($query) use ($request) {
                    return $query->where('subsidiary_id', $request->subsidiary_id);
                }),
            ],
            'purpose'           => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.unit'      => 'required|string|max:50',
            'items.*.remark' => 'nullable|string|max:255',
        ], [
            'request_number.unique' => 'Nomor RO sudah digunakan di plant ini.',
        ]);

        // Simpan header Request Order
        $ro = RequestOrder::create([
            'subsidiary_id'  => $validated['subsidiary_id'],
            'division'       => $validated['division'],
            'request_date'   => $validated['request_date'],
            'request_number' => $validated['request_number'],
            'purpose'        => $validated['purpose'],
            'status'         => 'pending',
            'requested_by'   => Auth::id(),
        ]);

        // Simpan detail barang
        foreach ($validated['items'] as $item) {
            $ro->items()->create([
                'item_name' => $item['item_name'],
                'quantity'  => $item['quantity'],
                'unit'      => $item['unit'],
            ]);
        }

        return redirect()->route('request-order.index')
            ->with('alert', 'Request Order berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil Request Order beserta relasi items dan user
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestOrder $requestOrder)
    {
        // Hapus item terkait
        $requestOrder->items()->delete();

        // Hapus header Request Order
        $requestOrder->delete();

        return redirect()->route('request-order.index')
            ->with('alert', 'Request Order berhasil dihapus.');
    }

    public function approveDivHead(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        $requestOrder->update([
            'status' => 'approved_by_div_head',
            'approved_by_div_head' => Auth::id(),
            'approved_by_divhead_at' => now(),
        ]);
        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('alert', 'Request Order telah disetujui oleh Kepala Divisi.');
        }

        return redirect()->route('request-order.index')
            ->with('alert', 'Request Order telah disetujui oleh Kepala Divisi.');
    }

    public function approveManager(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        if ($requestOrder->subsidiary_id == 2) {
            $requestOrder->update([
                'status'                 => 'approved_by_bod',
                'approved_by_bod'        => Auth::id(),
                'approved_by_bod_at'     => now(),
                'approved_by_manager'    => Auth::id(),
                'approved_by_manager_at' => now(),
            ]);
            if ($request->input('from') === 'show') {
                return redirect()->route('request-order.show', $id)
                    ->with('alert', 'Request Order telah disetujui.');
            }
            return redirect()->route('request-order.index')
                ->with('alert', 'Request Order telah disetujui.');
        } else {
            $requestOrder->update([
                'status'                 => 'approved_by_manager',
                'approved_by_manager'    => Auth::id(),
                'approved_by_manager_at' => now(),
            ]);
            if ($request->input('from') === 'show') {
                return redirect()->route('request-order.show', $id)
                    ->with('alert', 'Request Order telah disetujui Plant Manager.');
            }
            return redirect()->route('request-order.index')
                ->with('alert', 'Request Order telah disetujui Plant Manager.');
        }
    }

    public function approveBod(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        $requestOrder->update([
            'status' => 'approved_by_bod',
            'approved_by_bod' => Auth::id(),
            'approved_by_bod_at' => now(),
        ]);
        if ($request->input('from') === 'show') {
            return redirect()->route('request-order.show', $id)
                ->with('alert', 'Request Order telah disetujui BOD.');
        }
        return redirect()->route('request-order.index')
            ->with('alert', 'Request Order telah disetujui BOD.');
    }
    public function pdf($id)
    {
        $order = RequestOrder::with(['items', 'requester', 'subsidiary', 'divHead', 'plantManager'])->findOrFail($id);
        $timestamp = now()->format('d/m/Y H:i:s');
        $pdf = Pdf::loadView('purchasing.request_order.pdf', compact('order', 'timestamp'));
        return $pdf->stream('RO-' . $order->request_number . '.pdf');
    }
}
