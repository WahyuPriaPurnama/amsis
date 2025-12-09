@extends('layouts.app')
@section('title', 'Detail RO')
@section('menuRO', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Detail Request Order
            @endslot
            <div class="text-end">
                <x-buttons.delete2 href="{{ route('request-order.destroy', $order->id) }}"></x-buttons.delete2>
            </div>
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



            {{-- Barang dinamis --}}
            <h5 class="fw-semibold mb-3">Daftar Barang</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ $item->remark ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Note:</strong> {{ $order->purpose }}
                </div>
            </div>
            {{-- Status & User --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>Dibuat Oleh:</strong> {{ $order->user->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>Kepala Divisi:</strong> {{ $order->divHead->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>Plant Manager:</strong> {{ $order->manager->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>BOD:</strong> {{ $order->bod->name ?? '-' }}
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <a href="{{ route('request-order.index') }}" class="btn btn-secondary">Kembali</a>
                @if ($order->status === 'pending')
                    {{-- Tombol untuk Kepala Divisi --}}
                    <form action="{{ route('request-order.approve_div_head', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            Approve Kepala Divisi
                        </button>
                    </form>
                @elseif($order->status === 'approved_by_div_head')
                    {{-- Tombol untuk Plant Manager --}}
                    <form action="{{ route('request-order.approve_manager', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            Approve Plant Manager
                        </button>
                    </form>
                @elseif($order->status === 'approved_by_manager')
                    {{-- Tombol untuk Plant Manager --}}
                    <form action="{{ route('request-order.approve_bod', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            Approve BOD
                        </button>
                    </form>
                @else
                    <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
                @endif
            </div>
        @endcomponent
    </div>
@endsection
