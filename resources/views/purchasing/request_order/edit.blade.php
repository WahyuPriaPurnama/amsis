@extends('layouts.app')
@section('title', 'Edit Request Order')
@section('menuOrder', 'active')

@section('content')
<div class="container mt-3" x-data="roEditForm">
    @component('components.card')
    @slot('header')
    <div class="d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2"></i>Edit Request Order</span>
        <span class="badge bg-secondary" x-text="requestNumber"></span>
    </div>
    @endslot

    {{-- Form Update --}}
    <form action="{{ route('request-order.update', $order->id) }}" method="POST" enctype="multipart/form-data" @submit="submitForm">
        @csrf
        @method('PUT')

        {{-- Alert Validasi Error --}}
        @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Info Umum --}}
        <h5 class="fw-bold mb-3 text-primary border-bottom pb-2">Informasi Umum</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Plant / Subsidiary <span class="text-danger">*</span></label>
                <select name="subsidiary_id" x-ref="subsidiary_id" @change="updateRequestNumber"
                    class="form-select @error('subsidiary_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Plant --</option>
                    @foreach ($subsidiaries as $sub)
                    <option value="{{ $sub->id }}"
                        {{ old('subsidiary_id', $order->subsidiary_id) == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small">Divisi <span class="text-danger">*</span></label>
                <input type="text" name="division" class="form-control @error('division') is-invalid @enderror"
                    value="{{ old('division', $order->division) }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small">Tanggal Request <span class="text-danger">*</span></label>
                <input type="date" name="request_date" x-ref="request_date" @change="updateRequestNumber"
                    class="form-control @error('request_date') is-invalid @enderror"
                    value="{{ old('request_date', \Carbon\Carbon::parse($order->request_date)->format('Y-m-d')) }}"
                    required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small">Nomor RO <span class="text-danger">*</span></label>
                <input type="text" readonly name="request_number" x-model="requestNumber"
                    class="form-control bg-light @error('request_number') is-invalid @enderror" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">Lampiran RO (Header)</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                    accept=".pdf,.jpg,.jpeg,.png">
                @if ($order->attachment)
                <div class="form-text mt-2">
                    File saat ini: <a href="{{ asset('storage/' . $order->attachment) }}" target="_blank">Lihat Lampiran</a>
                    <br><small class="text-muted">Biarkan kosong jika tidak ingin mengubah file.</small>
                </div>
                @endif
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">Catatan / Keperluan <span class="text-danger">*</span></label>
                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3" required>{{ old('purpose', $order->purpose) }}</textarea>
            </div>
        </div>

        {{-- Daftar Barang --}}
        <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
            <h5 class="fw-bold mb-0 text-primary border-bottom pb-2 w-100">
                <i class="bi bi-box-seam me-2"></i>Daftar Barang
            </h5>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th width="35%">Nama Barang <span class="text-danger">*</span></th>
                        <th width="15%">Qty <span class="text-danger">*</span></th>
                        <th width="15%">Satuan <span class="text-danger">*</span></th>
                        <th width="25%">Keterangan (Remark)</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <input type="hidden" :name="`items[${index}][id]`" x-model="item.id">
                            <td>
                                <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" class="form-control" required placeholder="Nama Barang">
                            </td>
                            <td>
                                <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" class="form-control text-center" required>
                            </td>
                            <td>
                                <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" class="form-control text-center" required placeholder="Pcs/Ltr/Kg">
                            </td>
                            <td>
                                <input type="text" :name="`items[${index}][remark]`" x-model="item.remark" class="form-control" placeholder="Opsional">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" @click="removeItem(index)" x-show="items.length > 1">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" @click="addItem">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris Barang
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-between mt-5 border-top pt-3">
            <a href="{{ route('request-order.show', $order->id) }}" class="btn btn-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-success px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
    @endcomponent
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('roEditForm', () => {
            const initialItems = @json(old('items', $order->items));
            const originalSubsidiaryId = "{{ $order->subsidiary_id }}";
            const originalRequestNumber = "{{ $order->request_number }}";

            return {
                requestNumber: "{{ old('request_number', $order->request_number) }}",
                items: initialItems.map(item => ({
                    id: item.id || '',
                    item_name: item.item_name || '',
                    quantity: item.quantity || 1,
                    unit: item.unit || '',
                    remark: item.remark || ''
                })),

                async updateRequestNumber() {
                    const subsidiaryId = this.$refs.subsidiary_id.value;
                    const requestDate = this.$refs.request_date.value;

                    if (!subsidiaryId) {
                        this.requestNumber = '';
                        return;
                    }

                    // Jika Plant dan Tanggal kembali seperti semula saat pertama di-edit, pakai nomor RO asli
                    if (subsidiaryId == originalSubsidiaryId && requestDate == "{{ \Carbon\Carbon::parse($order->request_date)->format('Y-m-d') }}") {
                        this.requestNumber = originalRequestNumber;
                        return;
                    }

                    // Jika Plant atau Tanggal diganti, fetch nomor RO baru via AJAX
                    try {
                        let response = await fetch(`{{ route('request-order.get-next-number') }}?subsidiary_id=${subsidiaryId}&request_date=${requestDate}`);
                        let data = await response.json();
                        this.requestNumber = data.request_number;
                    } catch (error) {
                        console.error('Gagal mengambil nomor RO baru:', error);
                    }
                },

                addItem() {
                    this.items.push({
                        id: '',
                        item_name: '',
                        quantity: 1,
                        unit: '',
                        remark: ''
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            };
        });
    });
</script>
@endpush