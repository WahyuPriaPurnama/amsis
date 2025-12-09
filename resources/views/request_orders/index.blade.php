@extends('layouts.app')
@section('title', 'Data RO')
@section('menuRO', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between flex-wrap">
                @unless (auth()->user()->hasRole('employee'))
                    <div class="d-flex gap-2 flex-wrap">
                        <x-buttons.create href="{{ route('request-order.create') }}">Buat RO</x-buttons.create>
                    </div>
                @endunless
            </div>
            @slot('header')
                Data RO
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th>Plant</th> --}}
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
                                {{-- <td>{{ $order->subsidiary->name ?? '-' }}</td> --}}
                                <td>{{ $order->division }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->request_date)->format('d-m-Y') }}</td>
                                <td><a href="{{ route('request-order.show', $order->id) }}" class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">
                                        {{ $order->request_number }}
                                    </a></td>
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
                                            <spa n class="badge bg-warning text-dark">Menunggu Persetujuan Plant Manager</span>
                                            @break

                                            @case('approved_by_manager')
                                                <span class="badge bg-warning text-dark">Menunggu Persetujuan BOD</span>
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
                                    @if ($order->status === 'pending')
                                        {{-- Tombol untuk Kepala Divisi --}}
                                        <form action="{{ route('request-order.approve_div_head', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                Approve Kepala Divisi
                                            </button>
                                        </form>
                                    @elseif($order->status === 'approved_by_div_head')
                                        {{-- Tombol untuk Plant Manager --}}
                                        <form action="{{ route('request-order.approve_manager', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                Approve Plant Manager
                                            </button>
                                        </form>
                                    @elseif($order->status === 'approved_by_manager')
                                        {{-- Tombol untuk Plant Manager --}}
                                        <form action="{{ route('request-order.approve_bod', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                Approve BOD
                                            </button>
                                        </form>
                                    @else
                                        <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
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
