@extends('layouts.app')
@section('title', 'Penerimaan')
@section('menuReceipt', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck-loading me-2"></i> Daftar Kedatangan Barang</span>
                    <a href="{{ route('receipts.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Input Kedatangan
                    </a>
                </div>
            @endslot

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Tanggal Datang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receipts as $receipt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $receipt->supplier->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->arrival_date)->format('d M Y') }}</td>
                                <td>
                                    <!-- Tambahkan tombol aksi jika diperlukan -->
                                    <a href="{{ route('receipts.show', $receipt->id) }}"
                                        class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
