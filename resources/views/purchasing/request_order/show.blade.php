@extends('layouts.app')
@section('title', 'Detail Request Order')
@section('menuOrder', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Detail Request Order
            @endslot
            <div class="row mt-3">
                <div class="col-12 col-md-8 mb-2 mb-md-0">
                    @switch($order->status)
                        @case('pending')
                            <span class="badge bg-warning text-dark fs-5 w-100 text-center">
                                Menunggu Persetujuan Kepala Divisi
                            </span>
                        @break

                        @case('approved_by_div_head')
                            <span class="badge bg-warning text-dark fs-5 w-100 text-center">
                                Menunggu Persetujuan Plant Manager
                            </span>
                        @break

                        @case('approved_by_manager')
                            @if ($order->subsidiary->id == 2)
                                <span class="badge bg-success fs-5 w-100 text-center">
                                    Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark fs-5 w-100 text-center">
                                    Menunggu Persetujuan BOD
                                </span>
                            @endif
                        @break

                        @case('approved_by_bod')
                            <span class="badge bg-success fs-5 w-100 text-center">Approved</span>
                        @break

                        @default
                            <span class="badge bg-secondary fs-5 w-100 text-center">{{ $order->status }}</span>
                        @break
                    @endswitch
                </div>
                @can('request-order.delete')
                    <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-center">
                        <x-buttons.delete2 href="{{ route('request-order.destroy', $order->id) }}"></x-buttons.delete2>
                    </div>
                @endcan
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
                    <strong>Plant Manager:</strong> {{ $order->plantManager->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>BOD:</strong> {{ $order->bod->name ?? '-' }}
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <a href="{{ route('request-order.index') }}" class="btn btn-secondary">Kembali</a>
                <div class="d-flex gap-1">
                    <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
                    @can('request-order.approve')
                        @if ($order->status === 'pending')
                            <form action="{{ route('request-order.approve_div_head', ['id' => $order->id, 'from' => 'show']) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    Approve Divisi
                                </button>
                            </form>
                        @elseif($order->status === 'approved_by_div_head')
                            <form action="{{ route('request-order.approve_manager', ['id' => $order->id, 'from' => 'show']) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    Approve Manager
                                </button>
                            </form>
                        @elseif($order->status === 'approved_by_manager')
                            <form action="{{ route('request-order.approve_bod', ['id' => $order->id, 'from' => 'show']) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    Approve BOD
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        @endcomponent
    </div>
@endsection
