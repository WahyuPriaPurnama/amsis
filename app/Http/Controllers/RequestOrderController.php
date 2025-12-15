<?php

namespace App\Http\Controllers;

use App\Models\RequestOrder;
use App\Models\Subsidiary;
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

            return view('request_orders.index', compact('orders'));
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

        return view('request_orders.create', compact('subsidiaries'));
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
            ->with('success', 'Request Order berhasil disimpan.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil Request Order beserta relasi items dan user
        $order = RequestOrder::with(['items', 'requester', 'subsidiary', 'divHead', 'plantManager'])->findOrFail($id);

        return view('request_orders.show', compact('order'));
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
            ->with('success', 'Request Order berhasil dihapus.');
    }

    public function approveDivHead($id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        $requestOrder->update([
            'status' => 'approved_by_div_head',
            'approved_by_div_head' => Auth::id(),
            'approved_by_divhead_at' => now(),
        ]);

        return redirect()->route('request-order.index')
            ->with('success', 'Request Order telah disetujui oleh Kepala Divisi.');
    }

    public function approveManager($id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        if ($requestOrder->subsidiary_id == 2) {
            // Plant Manager merangkap BOD
            $requestOrder->update([
                'status' => 'approved_by_bod',
                'approved_by_bod' => Auth::id(),
                'approved_by_bod_at' => now(),
            ]);

            $requestOrder->update([
                'status' => 'approved_by_manager',
                'approved_by_manager' => Auth::id(),
                'approved_by_manager_at' => now(),
            ]);
        }

        return redirect()->route('request-order.index')
            ->with('success', 'Request Order telah disetujui oleh Manager.');
    }

    public function approveBod($id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        $this->authorize('approve', $requestOrder);

        $requestOrder->update([
            'status' => 'approved_by_bod',
            'approved_by_bod' => Auth::id(),
            'approved_by_bod_at' => now(),
        ]);

        return redirect()->route('request-order.index')
            ->with('success', 'Request Order telah disetujui oleh BOD.');
    }
    public function pdf($id)
    {
        $order = RequestOrder::with(['items', 'requester', 'subsidiary', 'divHead', 'plantManager'])->findOrFail($id);
        $timestamp = now()->format('d/m/Y H:i:s');
        $pdf = Pdf::loadView('request_orders.pdf', compact('order', 'timestamp'));
        return $pdf->stream('RO-' . $order->request_number . '.pdf');
    }
}
