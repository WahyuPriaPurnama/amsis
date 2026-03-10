<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\MasterSupplier;
use Illuminate\Http\Request;

class MasterSupplierController extends Controller
{
    public function index()
    {
        $suppliers = MasterSupplier::latest()->get();
        return view('purchasing.master_supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('purchasing.master_supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:master_supplier,code',
            'name' => 'required',
            'type' => 'required',
            'contact_person' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'npwp' => 'nullable',
            'bank_name' => 'nullable',
            'bank_account_number' => 'nullable',
            'term_of_payment' => 'nullable|integer',
        ]);

        MasterSupplier::create($request->all());

        return redirect()->route('master-supplier.index')
            ->with('success', 'Master Supplier created successfully.');
    }
}
