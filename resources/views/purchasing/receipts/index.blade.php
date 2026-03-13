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
                            <th>No. Referensi</th>
                            <th>Supplier</th>
                            <th>Tanggal Datang</th>
                            <th>Penerima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receipts as $receipt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @can('master-supplier.show')
                                        <a href="{{ route('receipts.show', $receipt->id) }}">{{ $receipt->reference_number }}</a>
                                    @else
                                        {{ $receipt->reference_number }}
                                    @endcan
                                </td>
                                <td>{{ $receipt->supplier->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->arrival_date)->format('d M Y') }}</td>
                                <td>{{ $receipt->received_by }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
