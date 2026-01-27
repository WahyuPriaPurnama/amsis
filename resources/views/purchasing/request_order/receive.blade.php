@extends('layouts.app')
@section('title', 'Realisasi Penerimaan Barang')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container mt-3" x-data="receiveForm()">
        <form action="{{ route('request-order.update-receive', $requestOrder->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @component('components.card')
                @slot('header')
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-truck me-2"></i>Update Penerimaan:
                            {{ $requestOrder->request_number }}</span>
                        <span class="badge bg-dark">{{ $requestOrder->division }}</span>
                    </div>
                @endslot

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-2"></i> Silakan isi jumlah barang yang diterima dan tanggal kedatangan untuk
                    setiap item.
                </div>

                {{-- LOOPING ITEM MENGGUNAKAN ALPINE --}}
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="card mb-3 border shadow-sm">
                        <div class="card-body">
                            <div class="row g-3 align-items-center">
                                {{-- Info Barang (Read Only) --}}
                                <div class="col-md-4">
                                    <label class="small text-muted fw-bold d-block">Nama Barang</label>
                                    <span class="fw-bold text-primary" x-text="item.item_name"></span>
                                    <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                                </div>

                                <div class="col-md-2 text-center border-start border-end">
                                    <label class="small text-muted fw-bold d-block">Qty Permintaan</label>
                                    <span class="fs-5 fw-bold" x-text="item.quantity + ' ' + item.unit"></span>
                                </div>

                                {{-- Input Realisasi --}}
                                <div class="col-md-2">
                                    <label class="small fw-bold text-success">Qty Diterima</label>
                                    <input type="number" :name="`items[${index}][qty_received]`" x-model="item.qty_received"
                                        class="form-control border-success" :max="item.quantity" min="0" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="small fw-bold">Tgl Datang</label>
                                    <input type="date" :name="`items[${index}][date_received]`" x-model="item.date_received"
                                        class="form-control" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="small fw-bold">Bukti (File)</label>
                                    <input type="file" :name="`items[${index}][receipt_attachment]`"
                                        class="form-control form-control-sm">
                                    <template x-if="item.old_attachment">
                                        <div class="mt-1 small">
                                            <a :href="'/storage/' + item.old_attachment" target="_blank"
                                                class="text-decoration-none">
                                                <i class="bi bi-paperclip"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Baris Tambahan: PO Info --}}
                            <div class="row g-3 mt-2 pt-2 border-top">
                                <div class="col-md-3">
                                    <input type="text" :name="`items[${index}][po_number]`" x-model="item.po_number"
                                        class="form-control form-control-sm" placeholder="No. PO (Opsional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" :name="`items[${index}][po_date]`" x-model="item.po_date"
                                        class="form-control form-control-sm" title="Tanggal PO">
                                </div>
                                <div class="col-md-6 text-end">
                                    {{-- Progress bar sederhana --}}
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            :style="`width: ${(item.qty_received / item.quantity) * 100}%`" aria-valuenow="0"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted"
                                        x-text="'Persentase: ' + Math.round((item.qty_received / item.quantity) * 100) + '%'"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="mt-4 border-top pt-3 d-flex justify-content-between">
                    <a href="{{ route('request-order.show', $requestOrder->id) }}" class="btn btn-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow">
                        <i class="bi bi-check-circle me-2"></i>Simpan Realisasi Penerimaan
                    </button>
                </div>
            @endcomponent
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('receiveForm', () => {
                // 1. Definisikan data di variabel PHP terlebih dahulu
                @php
                    $mappedItems = $requestOrder->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_name' => $item->item_name,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'qty_received' => $item->qty_received ?? 0,
                            'date_received' => $item->date_received ?? date('Y-m-d'),
                            'po_number' => $item->po_number ?? '',
                            'po_date' => $item->po_date ?? '',
                            'old_attachment' => $item->receipt_attachment,
                        ];
                    });
                @endphp

                // 2. Ambil data JSON ke dalam konstanta JavaScript yang bersih
                const serverData = @json($mappedItems);

                return {
                    // 3. Masukkan ke dalam state Alpine
                    items: serverData,

                    // Tambahkan fungsi pendukung jika diperlukan (seperti validasi qty)
                    validateQty(item) {
                        if (parseInt(item.qty_received) > parseInt(item.quantity)) {
                            alert('Qty Diterima tidak boleh melebihi Qty Permintaan!');
                            item.qty_received = item.quantity;
                        }
                    }
                };
            });
        });
    </script>
@endpush
