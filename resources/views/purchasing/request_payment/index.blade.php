@extends('layouts.app')
@section('title', 'Data Request Payment')
@section('menuRP', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between flex-wrap">
                @can('request-payment.create')
                    <x-buttons.create href="{{ route('request-payment.create') }}">Buat RFP</x-buttons.create>
                @endcan
                <form method="GET" action="{{ route('request-payment.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari...">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
            @slot('header')
                List Request Payment
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plant</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>No. RO</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <th>{{ $payments->firstItem() + $loop->iteration - 1 }}</th>
                                <td>{{ $payment->subsidiary->name ?? '-' }}</td>
                                <td>{{ $payment->division }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</td>
                                <td>
                                    @can('request-payment.view')
                                        <a href="{{ route('request-payment.show', $payment->id) }}" class="text-decoration-none"
                                            data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">
                                            {{ $payment->payment_number }}
                                        </a>
                                    @else
                                        <div class="text-muted">
                                            {{ $payment->payment_number }}
                                        </div>
                                    @endcan
                                </td>
                                <td>
                                    <ul class="mb-0">
                                        @foreach ($payment->items as $item)
                                            <li>{{ $item->item_name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @switch($payment->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Menunggu Persetujuan Manager</span>
                                        @break

                                        @case('approved_by_manager')
                                            <span class="badge bg-warning text-dark">Menunggu Persetujuan BOD</span>
                                        @break

                                        @case('approved_by_bod')
                                            <span class="badge bg-success">Approved</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">{{ $payment->status }}</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @can('request-payment.approve')
                                        @include('purchasing.partials.payment-approve-button')
                                    @endcan
                                    @if ($payment->status === 'approved_by_bod')
                                        <x-buttons.pdf href="{{ route('request-payment.pdf', $payment->id) }}"></x-buttons.pdf>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $payments->links() }}
            </div>
        @endcomponent
    </div>
@endsection
