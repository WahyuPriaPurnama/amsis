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
                Rule::unique('request_orders')->where(
                    fn($q) =>
                    $q->where('subsidiary_id', $request->subsidiary_id)
                ),
            ],
            'purpose'           => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255|distinct',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.unit'      => 'required|string|max:50',
            'items.*.remark'    => 'nullable|string|max:255',
        ], [
            'request_number.unique' => 'Nomor RO sudah digunakan di plant ini.',
        ]);

        $ro = RequestOrder::create([
            'subsidiary_id'  => $validated['subsidiary_id'],
            'division'       => $validated['division'],
            'request_date'   => $validated['request_date'],
            'request_number' => $validated['request_number'],
            'purpose'        => $validated['purpose'] ?? null,
            'status'         => 'pending',
            'requested_by'   => Auth::id(),
        ]);

        $ro->items()->createMany(
            collect($validated['items'])->map(fn($item) => [
                'item_name' => $item['item_name'],
                'quantity'  => $item['quantity'],
                'unit'      => $item['unit'],
                'remark'    => $item['remark'] ?? null,
            ])->toArray()
        );

        return redirect()
            ->route('request-order.index')
            ->with('success', 'Request Order berhasil dibuat.');
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
        //
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
