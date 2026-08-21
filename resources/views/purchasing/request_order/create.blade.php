@extends('layouts.app')
@section('title', 'Buat Request Order')
@section('menuOrder', 'active')

@section('content')
<div class="container mt-3" x-data="roForm">
    <form action="{{ route('request-order.store') }}" method="POST" enctype="multipart/form-data"
        @submit.prevent="submitForm">
        @csrf

        @component('components.card')
        @slot('header')
        <span class="fw-bold text-uppercase">Tambah Request Order</span>
        @endslot

        {{-- SECTION 1: HEADER --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-bold">Plant</label>
                <select name="subsidiary_id" x-ref="subsidiary_id"
                    @change="updateRequestNumber"
                    class="form-select @error('subsidiary_id') is-invalid @enderror">
                    <option value="">Pilih Plant...</option>
                    @foreach ($subsidiaries as $subsidiary)
                    <option value="{{ $subsidiary->id }}"
                        {{ old('subsidiary_id', $subsidiaries->first()->id ?? '') == $subsidiary->id ? 'selected' : '' }}>
                        {{ $subsidiary->name }}
                    </option>
                    @endforeach
                </select>
                <small class="text-danger" x-text="errors.subsidiary_id"></small>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Divisi</label>
                <input type="text" name="division" x-ref="division" value="{{ old('division') }}"
                    class="form-control @error('division') is-invalid @enderror" placeholder="Nama Divisi">
                <small class="text-danger" x-text="errors.division"></small>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date" name="request_date" x-ref="request_date"
                    @change="updateRequestNumber"
                    value="{{ old('request_date', date('Y-m-d')) }}"
                    class="form-control @error('request_date') is-invalid @enderror">
                <small class="text-danger" x-text="errors.request_date"></small>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Nomor RO</label>
                <input type="text" readonly name="request_number" x-ref="request_number"
                    x-model="requestNumber"
                    class="form-control @error('request_number') is-invalid @enderror"
                    placeholder="Terisi Otomatis">
                <small class="text-danger" x-text="errors.request_number"></small>
            </div>
        </div>

        <hr class="my-4">

        {{-- SECTION 2: DYNAMIC ITEMS --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-list-task me-2"></i>Daftar Barang</h5>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" @click="addItem">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
            </button>
        </div>

        <template x-for="(item, index) in items" :key="index">
            <div class="card mb-3 border shadow-sm item-row">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name"
                                class="form-control" placeholder="Input nama barang...">
                            <small class="text-danger" x-text="errors[`item_name_${index}`]"></small>
                        </div>
                        <div class="col-md-1 text-center">
                            <label class="small text-muted fw-bold d-block text-center">Qty <span class="text-danger">*</span></label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                class="form-control text-center">
                            <small class="text-danger" x-text="errors[`quantity_${index}`]"></small>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" :name="`items[${index}][unit]`" x-model="item.unit"
                                class="form-control" placeholder="Pcs/Set/Ltr">
                            <small class="text-danger" x-text="errors[`unit_${index}`]"></small>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Keterangan / Spec</label>
                            <input type="text" :name="`items[${index}][remark]`" x-model="item.remark"
                                class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger border-0" @click="removeItem(index)"
                                x-show="items.length > 1" title="Hapus baris">
                                <i class="bi bi-trash-fill fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- SECTION 3: FOOTER --}}
        <div class="row mt-4 pt-3 border-top">
            <div class="col-md-8">
                <label class="form-label fw-bold text-muted small uppercase">Catatan Tambahan (Header)</label>
                <textarea name="purpose" rows="2" class="form-control"
                    placeholder="Tulis alasan permintaan atau instruksi khusus...">{{ old('purpose') }}</textarea>
            </div>
            <div class="col-md-4 text-end">
                <label class="form-label fw-bold text-muted small d-block">Lampiran Utama RO</label>
                <input type="file" name="attachment" class="form-control shadow-sm">
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success px-5 fw-bold shadow">
                SIMPAN REQUEST ORDER
            </button>
        </div>
        @endcomponent
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('roForm', () => {
            @php
            $defaultItems = [
                [
                    'item_name' => '',
                    'quantity' => 1,
                    'unit' => '',
                    'remark' => '',
                ],
            ];
            @endphp

            const initialItems = @json(old('items', $defaultItems));

            return {
                // BUG FIX 1: Perbaikan string concatenation & line break pada old()
                requestNumber: "{{ old('request_number', $autoRequestNumber ?? '') }}",

                items: initialItems.map(item => ({
                    item_name: item.item_name || '',
                    quantity: item.quantity || 1,
                    unit: item.unit || '',
                    remark: item.remark || ''
                })),

                errors: {},

                // Menjalankan fungsi saat komponen pertama kali dimuat
                init() {
                    // Jika nomor RO belum ada tapi Plant sudah terpilih, ambil nomor otomatis
                    if (!this.requestNumber && this.$refs.subsidiary_id?.value) {
                        this.updateRequestNumber();
                    }
                },

                async updateRequestNumber() {
                    const subsidiaryId = this.$refs.subsidiary_id.value;
                    const requestDate = this.$refs.request_date.value;

                    if (!subsidiaryId) {
                        this.requestNumber = '';
                        return;
                    }

                    try {
                        let response = await fetch(`{{ route('request-order.get-next-number') }}?subsidiary_id=${subsidiaryId}&request_date=${requestDate}`);
                        let data = await response.json();
                        this.requestNumber = data.request_number;
                    } catch (error) {
                        console.error('Gagal mengambil nomor RO:', error);
                    }
                },

                addItem() {
                    this.items.push({
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
                },

                submitForm(e) {
                    this.errors = {};

                    if (!this.$refs.subsidiary_id.value) this.errors.subsidiary_id = 'Pilih Plant!';
                    if (!this.$refs.division.value) this.errors.division = 'Divisi wajib diisi!';
                    if (!this.$refs.request_date.value) this.errors.request_date = 'Tanggal wajib diisi!';
                    if (!this.$refs.request_number.value) this.errors.request_number = 'Nomor RO wajib diisi!';

                    this.items.forEach((item, i) => {
                        if (!item.item_name) this.errors[`item_name_${i}`] = 'Wajib!';
                        if (!item.quantity || item.quantity <= 0) this.errors[`quantity_${i}`] = 'Min 1!';
                        if (!item.unit) this.errors[`unit_${i}`] = 'Wajib!';
                    });

                    if (Object.keys(this.errors).length === 0) {
                        e.target.submit();
                    } else {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            };
        });
    });
</script>
@endpush