@extends('layouts.app')
@section('title', 'Input Kedatangan Barang')
@section('menuReceipt', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck-loading me-2"></i> Input Kedatangan Barang</span>
                    <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> Daftar Kedatangan
                    </a>
                </div>
            @endslot
            <div class="bg-blue-600 p-4 text-white">
                <h1 class="text-lg font-bold">Input Kedatangan Barang</h1>
                <p class="text-xs opacity-80">Catat dokumen fisik langsung dari kamera</p>
            </div>

            <form action="{{ route('receipts.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6">
                @csrf

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih Supplier</label>
                        <select name="supplier_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Datang</label>
                        <input type="date" name="arrival_date" value="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="space-y-6">
                    <h2 class="text-md font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Foto Dokumen Fisik
                    </h2>

                    <div class="bg-gray-50 p-3 rounded-xl border-2 border-dashed border-gray-300 text-center">
                        <label class="block text-sm font-bold text-gray-600 mb-2">SURAT JALAN</label>
                        <input type="text" name="doc_numbers[SURAT_JALAN]" placeholder="Nomor Surat Jalan"
                            class="w-full mb-2 text-sm border-gray-300 rounded-md">
                        <input type="file" name="files[SURAT_JALAN]" accept="image/*" capture="environment"
                            class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border-2 border-dashed border-gray-300 text-center">
                        <label class="block text-sm font-bold text-gray-600 mb-2">PURCHASE ORDER (PO)</label>
                        <input type="text" name="doc_numbers[PO]" placeholder="Nomor PO"
                            class="w-full mb-2 text-sm border-gray-300 rounded-md">
                        <input type="file" name="files[PO]" accept="image/*" capture="environment"
                            class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border-2 border-dashed border-gray-300 text-center">
                        <label class="block text-sm font-bold text-gray-600 mb-2">FAKTUR / INVOICE</label>
                        <input type="text" name="doc_numbers[FAKTUR]" placeholder="Nomor Faktur"
                            class="w-full mb-2 text-sm border-gray-300 rounded-md">
                        <input type="file" name="files[FAKTUR]" accept="image/*" capture="environment"
                            class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition duration-200">
                    Simpan Kedatangan Barang
                </button>
            </form>
        @endcomponent
    </div>
@endsection
