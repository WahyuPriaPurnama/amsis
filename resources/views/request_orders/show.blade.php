@extends('layouts.app')
@section('title', 'Detail RO')
@section('menuRO', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Detail Request Order
            @endslot

            {{-- Data umum --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Plant:</strong> {{ $order->subsidiary->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>Divisi:</strong> {{ $order->division }}
                </div>
                <div class="col-md-3">
                    <strong>Tanggal:</strong> {{ $order->request_date }}
                </div>
                <div class="col-md-3">
                    <strong>Nomor RO:</strong> {{ $order->request_number }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Tujuan:</strong> {{ $order->purpose }}
                </div>
            </div>

            {{-- Barang dinamis --}}
            <h5 class="fw-semibold mb-3">Daftar Barang</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Status & User --}}
            <div class="row mt-3">
                <div class="col-md-4">
                    <strong>Dibuat Oleh:</strong> {{ $order->user->name ?? '-' }}
                </div>
                <div class="col-md-4">
                    <strong>Approved Kadiv:</strong> {{ $order->divHead->name ?? '-' }}
                </div>
                <div class="col-md-4">
                    <strong>Approved Manager:</strong> {{ $order->manager->name ?? '-' }}
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <a href="{{ route('request-order.index') }}" class="btn btn-secondary">Kembali</a>
                <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
            </div>
        @endcomponent
    </div>
@endsection
