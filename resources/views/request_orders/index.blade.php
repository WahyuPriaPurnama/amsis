@extends('layouts.app')
@section('title', 'Data RO')
@section('menuRO', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between flex-wrap">
                @can('request-order.create')
                    <div class="d-flex gap-2 flex-wrap">
                        <x-buttons.create href="{{ route('request-order.create') }}">Buat RO</x-buttons.create>
                    </div>
                @endcan
            </div>
            @slot('header')
                Data RO
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plant</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>No. RO</th>
                            <th>Nama Barang</th>
                            <th>Status</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->subsidiary->name ?? '-' }}</td>
                                <td>{{ $order->division }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->request_date)->format('d-m-Y') }}</td>

                                <td>
                                    @can('request-order.view')
                                        <a href="{{ route('request-order.show', $order->id) }}" class="text-decoration-none"
                                            data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">
                                            {{ $order->request_number }}
                                        </a>
                                    @else
                                        <div class="text-muted">
                                            {{ $order->request_number }}
                                        </div>
                                    @endcan
                                </td>
                                <td>
                                    <ul class="mb-0">
                                        @foreach ($order->items as $item)
                                            <li>{{ $item->item_name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Menunggu Persetujuan Kepala Divisi</span>
                                        @break

                                        @case('approved_by_div_head')
                                            @if ($order->subsidiary->id == 2)
                                                <span class="badge bg-warning text-dark">Menunggu Persetujuan Plant Manager / BOD</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu Persetujuan Plant Manager</span>
                                            @endif
                                        @break

                                        @case('approved_by_manager')
                                            @if ($order->subsidiary->id == 2)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu Persetujuan BOD</span>
                                            @endif
                                        @break

                                        @case('approved_by_bod')
                                            <span class="badge bg-success">Approved</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @can('request-order.approve')
                                        @include('request_orders.partials.approve-button')
                                    @endcan
                                    @if ($order->status === 'approved_by_bod')
                                        @can('request-order.export')
                                            <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent

    </div>

@endsection
