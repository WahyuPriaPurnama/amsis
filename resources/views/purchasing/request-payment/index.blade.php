<div>
    <div class="container-fluid mt-3">
        <x-card>
            <x-slot:header>
                List Request Payment
            </x-slot:header>

            {{-- Action & Filter Bar --}}
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between align-items-center">
                <!-- Tombol Buat RFP -->
                <div>
                    @can('request-payment.create')
                    <x-buttons.create href="{{ route('request-payment.create') }}" wire:navigate>
                        Buat RFP
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

                    {{-- Input Search dengan Live Update --}}
                    <div class="input-group" style="min-width: 250px;">
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Cari No. RFP / Deskripsi...">

                        {{-- Indikator Loading --}}
                        <span class="input-group-text bg-white" wire:loading wire:target="search, subsidiary_id">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </span>

                        {{-- Tombol Reset Filter --}}
                        @if ($search || $subsidiary_id)
                        <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabel Request Payment --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">#</th>
                            <th>Plant</th>
                            <th>Tanggal</th>
                            <th>No. RFP</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                        <tr wire:key="payment-row-{{ $payment->id }}">
                            <th>{{ $payments->firstItem() + $loop->index }}</th>
                            <td>{{ $payment->subsidiary->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</td>
                            <td>
                                @can('request-payment.view')
                                <a href="{{ route('request-payment.show', $payment->id) }}"
                                    class="text-decoration-none fw-bold"
                                    wire:navigate
                                    title="Klik untuk lihat detail">
                                    {{ $payment->payment_number }}
                                </a>
                                @else
                                <span class="text-muted fw-bold">{{ $payment->payment_number }}</span>
                                @endcan
                            </td>
                            <td>
                                <ul class="mb-0 ps-3 small">
                                    @foreach ($payment->items as $item)
                                    <li>{{ $item->item_name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @switch($payment->status)
                                @case('pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu Acc Direktur</span>
                                @break
                                @case('approved_by_manager')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu Acc Direktur OP</span>
                                @break
                                @case('approved_by_bod')
                                <span class="badge bg-success">Disetujui</span>
                                @break
                                @default
                                <span class="badge bg-secondary">{{ $payment->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Data Request Payment tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Links --}}
                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </x-card>
    </div>
</div>