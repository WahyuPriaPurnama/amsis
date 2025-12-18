<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
use Illuminate\Http\Request;

class RequestPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payments = RequestPayment::orderBy('created_at', 'desc')->paginate(20);
        if ($request->input('search')) {
            $search = $request->input('search');
            $payments = RequestPayment::where('payment_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();
        }
        return view('purchasing.request_payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Pastikan user punya permission
        if (!$user->can('request-payment.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Request Payment.');
        }

        // Ambil daftar subsidiary (plant) untuk dropdown
        $subsidiaries = Subsidiary::orderBy('name')->get();

        return view('purchasing.request_payment.create', compact('subsidiaries'));
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
    public function show(RequestPayment $requestPayment)
    {
        //
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
        //
    }
}
