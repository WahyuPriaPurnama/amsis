@extends('layouts.app')
@section('title', 'Data Request Order')
@section('menuOrder', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between">
                @can('request-order.create')
                    <x-buttons.create href="{{ route('request-order.create') }}">Buat RO</x-buttons.create>
                @endcan

                <form method="GET" action="{{ route('request-order.index') }}" class="d-flex gap-2">
                    {{-- Filter Dropdown Plant --}}
                    <select name="subsidiary_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Plant --</option>
                        @foreach ($allSubsidiaries as $sub)
                            <option value="{{ $sub->id }}" {{ request('subsidiary_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Input Search --}}
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari No. RO / Divisi...">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        {{-- Tombol Reset untuk membersihkan semua filter --}}
                        @if (request('search') || request('subsidiary_id'))
                            <a href="{{ route('request-order.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            @slot('header')
                List Request Order
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Informasi Order</th> {{-- Penggabungan Plant, Divisi, Tgl --}}
                            <th>No. RO / PO</th>
                            <th>Nama Barang</th>
                            <th width="150">Realisasi (Qty / %)</th>
                            <th>Status</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $totalQtyRequested = $order->items->sum('quantity');
                                $totalQtyReceived = $order->items->sum('qty_received');
                                $percentage =
                                    $totalQtyRequested > 0 ? ($totalQtyReceived / $totalQtyRequested) * 100 : 0;

                                $barColor = 'bg-danger';
                                if ($percentage >= 100) {
                                    $barColor = 'bg-success';
                                } elseif ($percentage > 0) {
                                    $barColor = 'bg-info';
                                }
                            @endphp
                            <tr>
                                <th>{{ $orders->firstItem() + $loop->iteration - 1 }}</th>

                                {{-- KOLOM GABUNGAN: PLANT, DIVISI, TGL REQUEST --}}
                                <td>
                                    <div class="fw-bold text-primary">{{ $order->subsidiary->name ?? '-' }}</div>
                                    <div class="small text-dark">{{ $order->division }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i
                                            class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($order->request_date)->format('d/m/Y') }}
                                    </div>
                                </td>

                                {{-- KOLOM GABUNGAN: NO RO & INFO PO --}}
                                <td>
                                    <div class="mb-2">
                                        <span class="small text-muted d-block">Request Order:</span>
                                        <a href="{{ route('request-order.show', $order->id) }}"
                                            class="text-decoration-none fw-bold">
                                            {{ $order->request_number }}
                                        </a>
                                    </div>
                                    <div class="p-2 border rounded bg-white shadow-sm" style="min-width: 140px;">
                                        <span class="small text-muted d-block" style="font-size: 0.7rem;">Purchase Order:</span>
                                        @php
                                            // Mengambil info PO dari item pertama (asumsi satu RO satu PO)
                                            $firstItem = $order->items->whereNotNull('po_number')->first();
                                        @endphp
                                        @if ($firstItem)
                                            <div class="fw-bold text-success small">{{ $firstItem->po_number }}</div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <i
                                                    class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($firstItem->po_date)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="text-muted small italic" style="font-size: 0.7rem;">- No PO Data
                                                -</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom Nama Barang --}}
                                <td style="min-width: 250px;">
                                    <ul class="mb-0 small ps-3">
                                        @foreach ($order->items as $item)
                                            <li class="text-wrap" style="word-break: break-word;">
                                                <span class="fw-bold">{{ $item->item_name }}</span>
                                                <span class="text-muted">({{ $item->quantity }} {{ $item->unit }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>

                                {{-- Realisasi (Qty / %) --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-between mb-1 small">
                                            <span class="fw-bold text-dark">{{ $totalQtyReceived }} /
                                                {{ $totalQtyRequested }}</span>
                                            <span class="text-muted">{{ round($percentage) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if ($percentage >= 100 || $order->status === 'completed')
                                        <span class="badge bg-primary"><i class="bi bi-check-all me-1"></i>Selesai</span>
                                    @elseif($percentage > 0 || $order->status === 'partial')
                                        <span class="badge bg-info text-dark"><i class="bi bi-truck me-1"></i>Parsial</span>
                                    @else
                                        {{-- Switch status tetap sama --}}
                                        @switch($order->status)
                                            {{-- ... case status Anda ... --}}
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Wait Div Head</span>
                                            @break

                                            @case('approved_by_div_head')
                                                <span class="badge bg-warning text-dark">Wait Manager</span>
                                            @break

                                            @case('approved_by_manager')
                                                <span class="badge bg-warning text-dark">Wait BOD</span>
                                            @break

                                            @case('approved_by_bod')
                                                <span class="badge bg-success">Approved</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endswitch
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        @include('purchasing.partials.order-approve-button')
                                        @if (in_array($order->status, ['approved_by_bod', 'completed', 'partial']) || $percentage > 0)
                                            <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
        @endcomponent
    </div>
@endsection
