@extends('layouts.app')
@section('title', 'Detail Request Payment')
@section('menuPayment', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Detail Request Payment
            @endslot
            <div class="row mt-3 align-items-center mb-4">
                {{-- Kolom Kiri: Status Badge --}}
                <div class="col-12 col-md-8 mb-3 mb-md-0">
                    @switch($payment->status)
                        @case('pending')
                            <span class="badge bg-warning text-dark fs-6 p-2 w-100 text-center shadow-sm">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan Manager
                            </span>
                        @break

                        @case('approved_by_manager')
                            <span class="badge bg-warning text-dark fs-6 p-2 w-100 text-center shadow-sm">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan BOD
                            </span>
                        @break

                        @case('approved_by_bod')
                            <span class="badge bg-success fs-6 p-2 w-100 text-center shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Approved
                            </span>
                        @break

                        @default
                            <span class="badge bg-secondary fs-6 p-2 w-100 text-center shadow-sm">
                                {{ strtoupper($payment->status) }}
                            </span>
                        @break
                    @endswitch
                </div>

                {{-- Kolom Kanan: Action Buttons --}}
                <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-center gap-2">
                    {{-- Tombol Edit --}}
                    @can('request-payment.edit')
                            <a href="{{ route('request-payment.edit', $payment->id) }}"
                                class="btn btn-warning btn-sm shadow-sm d-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                    @endcan

                    {{-- Tombol Delete --}}
                    @can('request-payment.delete')
                            <x-buttons.delete2 href="{{ route('request-payment.destroy', $payment->id) }}"></x-buttons.delete2>
                       
                    @endcan
                </div>
            </div>

            {{-- Data umum --}}
            <div class="row mb-4 p-3 bg-light border rounded mx-0 shadow-sm">
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <span class="d-block small text-muted">Plant</span>
                    <strong class="fs-6">{{ $payment->subsidiary->name ?? '-' }}</strong>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <span class="d-block small text-muted">Divisi</span>
                    <strong class="fs-6">{{ $payment->division }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="d-block small text-muted">Tanggal</span>
                    <strong class="fs-6">{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="d-block small text-muted">Nomor Payment</span>
                    <strong class="fs-6 text-primary">{{ $payment->payment_number }}</strong>
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
                            <th>Harga Satuan</th>
                            <th>Jumlah Harga</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payment->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->amount, 2) }}</td>
                                <td>{{ $item->due_date ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Grand Total</td>
                            <td class="fw-bold">{{ number_format($payment->grand_total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Note:</strong> {{ $payment->purpose }}
                </div>



            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>Dibuat Oleh:</strong> {{ $payment->requester->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>Manager:</strong> {{ $payment->plantManager->name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>BOD:</strong> {{ $payment->bod->name ?? '-' }}
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <a href="{{ route('request-payment.index') }}" class="btn btn-secondary">Kembali</a>

                <div class="d-flex gap-1">


                    {{-- Tombol Lampiran --}}
                    @if ($payment->attachment)
                        <a href="{{ route('request-payment.attachment', $payment->id) }}" target="_blank"
                            class="btn btn-primary">Lampiran</a>
                    @endif

                    {{-- Tombol PDF --}}
                    <x-buttons.pdf href="{{ route('request-payment.pdf', $payment->id) }}"></x-buttons.pdf>

                    {{-- Approval Logic --}}
                    @can('request-payment.approve')
                        @if ($payment->status === 'pending')
                            <form action="{{ route('request-payment.approve_manager', ['id' => $payment->id, 'from' => 'show']) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    Approve Manager
                                </button>
                            </form>
                        @endif
                        @if ($payment->status === 'approved_by_manager')
                            <form action="{{ route('request-payment.approve_bod', ['id' => $payment->id, 'from' => 'show']) }}"
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
