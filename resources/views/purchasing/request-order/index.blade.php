<div>
    <div class="container-fluid mt-3">
        <x-card>
            <x-slot:header>
                List Request Order
            </x-slot:header>

            {{-- Action & Filter Bar --}}
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between align-items-center">
                <!-- Tombol Buat RO -->
                <div>
                    @can('request-order.create')
                    <x-buttons.create href="{{ route('request-order.create') }}" wire:navigate>
                        Buat RO
                    </x-buttons.create>
                    @endcan
                </div>

                <!-- Filter Plant & Search Reaktif -->
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- Filter Dropdown Plant --}}
                    <select wire:model.live="subsidiary_id" class="form-select" style="min-width: 180px;">
                        <option value="">-- Semua Plant --</option>
                        @foreach ($allSubsidiaries as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>

                    {{-- Input Search --}}
                    <div class="input-group" style="min-width: 250px;">
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Cari No. RO / Divisi / Barang...">

                        <span class="input-group-text bg-white" wire:loading wire:target="search, subsidiary_id">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </span>

                        {{-- Tombol Reset --}}
                        @if ($search || $subsidiary_id)
                        <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabel Request Order --}}
            <div class="table-responsive">
                <table class="table table-hover display align-middle" style="table-layout: fixed; min-width: 1000px; width: 100%;">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 160px;">Informasi Order</th>
                            <th style="width: 170px;">No. RO / PO</th>
                            <th style="width: 250px;">Nama Barang</th>
                            <th style="width: 150px;">Realisasi (Qty / %)</th>
                            <th style="width: 140px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                        @php
                        $totalQtyRequested = $order->items->sum('quantity');
                        $totalQtyReceived = $order->items->sum('qty_received');
                        $percentage = $totalQtyRequested > 0 ? ($totalQtyReceived / $totalQtyRequested) * 100 : 0;

                        $barColor = 'bg-danger';
                        if ($percentage >= 100) {
                        $barColor = 'bg-success';
                        } elseif ($percentage > 0) {
                        $barColor = 'bg-info';
                        }
                        @endphp
                        <tr wire:key="order-row-{{ $order->id }}">
                            <th>{{ $orders->firstItem() + $loop->index }}</th>

                            {{-- KOLOM GABUNGAN: PLANT, DIVISI, TGL REQUEST --}}
                            <td>
                                <div class="fw-bold text-primary text-truncate" title="{{ $order->subsidiary->name ?? '-' }}">
                                    {{ $order->subsidiary->name ?? '-' }}
                                </div>
                                <div class="small text-dark text-truncate" title="{{ $order->division }}">
                                    {{ $order->division }}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($order->request_date)->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- KOLOM GABUNGAN: NO RO & INFO PO --}}
                            <td>
                                <div class="mb-2">
                                    <span class="small text-muted d-block">Request Order:</span>
                                    <a href="{{ route('request-order.show', $order->id) }}"
                                        class="text-decoration-none fw-bold text-truncate d-block"
                                        wire:navigate>
                                        {{ $order->request_number }}
                                    </a>
                                </div>
                                <div class="p-2 border rounded bg-white shadow-sm">
                                    <span class="small text-muted d-block" style="font-size: 0.7rem;">Purchase Order:</span>
                                    @php
                                    $firstItem = $order->items->whereNotNull('po_number')->first();
                                    @endphp
                                    @if ($firstItem)
                                    <div class="fw-bold text-success small text-truncate" title="{{ $firstItem->po_number }}">
                                        {{ $firstItem->po_number }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">
                                        <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($firstItem->po_date)->format('d/m/Y') }}
                                    </div>
                                    @else
                                    <span class="text-muted small italic" style="font-size: 0.7rem;">- No PO Data -</span>
                                    @endif
                                </div>
                            </td>

                            {{-- KOLOM NAMA BARANG --}}
                            <td class="text-break" style="word-wrap: break-word; overflow-wrap: break-word;">
                                <ul class="mb-0 small ps-3">
                                    @foreach ($order->items as $item)
                                    <li class="text-wrap">
                                        <span class="fw-bold">{{ $item->item_name }}</span>
                                        <span class="text-muted">({{ $item->quantity }} {{ $item->unit }})</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- REALISASI (QTY / %) --}}
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="fw-bold text-dark">{{ $totalQtyReceived }} / {{ $totalQtyRequested }}</span>
                                        <span class="text-muted">{{ round($percentage) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $barColor }}"
                                            role="progressbar"
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td>
                                @switch($order->status)
                                @case('pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Persetujuan Kep. Divisi</span>
                                @break
                                @case('approved_by_div_head')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Persetujuan Manager</span>
                                @break
                                @case('approved_by_manager')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Persetujuan BOD</span>
                                @break
                                @case('approved_by_bod')
                                <span class="badge bg-success">Approved</span>
                                @break
                                @default
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data Request Order ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Links --}}
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </x-card>
    </div>
</div>