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
                            class="form-select @error('subsidiary_id') is-invalid @enderror">
                            <option value="">Pilih Plant...</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option value="{{ $subsidiary->id }}"
                                    {{ old('subsidiary_id') == $subsidiary->id ? 'selected' : '' }}>
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
                        <input type="date" name="request_date" x-ref="request_date" value="{{ old('request_date') }}"
                            class="form-control @error('request_date') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.request_date"></small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nomor RO</label>
                        <input type="text" name="request_number" x-ref="request_number" value="{{ old('request_number') }}"
                            class="form-control @error('request_number') is-invalid @enderror"
                            placeholder="Contoh: RO/2024/001">
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
                            {{-- Input Utama --}}
                            <div class="row g-2 align-items-end mb-3">
                                <div class="col-md-4">
                                    <label class="small text-muted fw-bold">Nama Barang <span
                                            class="text-danger">*</span></label>
                                    <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name"
                                        class="form-control" placeholder="Input nama barang...">
                                    <small class="text-danger" x-text="errors[`item_name_${index}`]"></small>
                                </div>
                                <div class="col-md-1 text-center">
                                    <label class="small text-muted fw-bold d-block text-center">Qty <span
                                            class="text-danger">*</span></label>
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

                            {{-- Info Penerimaan (Akan diisi setelah RO dibuat) --}}
                            <div class="row g-2 p-2 rounded-2 border"
                                style="background-color: #fcfcfc; border-style: dashed !important;">
                                <div class="col-12 mb-1">
                                    <span class="badge bg-light text-dark border"><i class="bi bi-truck me-1"></i> Area
                                        Logistik</span>
                                    <small class="text-muted ms-2">(Diisi jika barang sudah diproses/datang)</small>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" :name="`items[${index}][po_number]`" x-model="item.po_number"
                                        class="form-control form-control-sm" placeholder="No PO">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" :name="`items[${index}][po_date]`" x-model="item.po_date"
                                        class="form-control form-control-sm" title="Tanggal PO">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" :name="`items[${index}][qty_received]`" x-model="item.qty_received"
                                        class="form-control form-control-sm" placeholder="Qty Datang">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" :name="`items[${index}][date_received]`"
                                        x-model="item.date_received" class="form-control form-control-sm"
                                        title="Tanggal Terima">
                                </div>
                                <div class="col-md-3">
                                    <input type="file" :name="`items[${index}][receipt_attachment]`"
                                        class="form-control form-control-sm" title="Upload Bukti Terima">
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
        // Kita gunakan event 'alpine:init'
        document.addEventListener('alpine:init', () => {
            console.log('Registering roForm...'); // Untuk debugging

            Alpine.data('roForm', () => {
                // Definisikan data awal di sini
                @php
                    $defaultItems = [
                        [
                            'item_name' => '',
                            'quantity' => 1,
                            'unit' => '',
                            'remark' => '',
                            'po_number' => '',
                            'po_date' => '',
                            'qty_received' => 0,
                            'date_received' => '',
                        ],
                    ];
                @endphp

                const initialItems = @json(old('items', $defaultItems));

                return {
                    items: initialItems.map(item => ({
                        item_name: item.item_name || '',
                        quantity: item.quantity || 1,
                        unit: item.unit || '',
                        remark: item.remark || '',
                        po_number: item.po_number || '',
                        po_date: item.po_date || '',
                        qty_received: item.qty_received || 0,
                        date_received: item.date_received || ''
                    })),

                    errors: {},

                    addItem() {
                        console.log('Adding item row');
                        this.items.push({
                            item_name: '',
                            quantity: 1,
                            unit: '',
                            remark: '',
                            po_number: '',
                            po_date: '',
                            qty_received: 0,
                            date_received: ''
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
                        if (!this.$refs.request_date.value) this.errors.request_date =
                            'Tanggal wajib diisi!';
                        if (!this.$refs.request_number.value) this.errors.request_number =
                            'Nomor RO wajib diisi!';

                        this.items.forEach((item, i) => {
                            if (!item.item_name) this.errors[`item_name_${i}`] = 'Wajib!';
                            if (!item.quantity || item.quantity <= 0) this.errors[`quantity_${i}`] =
                                'Min 1!';
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
