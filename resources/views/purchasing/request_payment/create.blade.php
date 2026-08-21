@extends('layouts.app')
@section('title', 'Buat Request Payment')
@section('menuPayment', 'active')

@section('content')
<div class="container mt-3" x-data="rpForm">
    @component('components.card')
    @slot('header')
    <span class="fw-bold text-uppercase">Create Request Payment</span>
    @endslot

    <form action="{{ route('request-payment.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
        @csrf

        {{-- SECTION 1: HEADER --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="subsidiary_id" class="form-label fw-bold small">Plant <span class="text-danger">*</span></label>
                <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                    x-ref="subsidiary_id"
                    @change="updatePaymentNumber">
                    <option value="">Pilih Plant</option>
                    @foreach ($subsidiaries as $subsidiary)
                    <option value="{{ $subsidiary->id }}"
                        {{ old('subsidiary_id', $subsidiaries->first()->id ?? '') == $subsidiary->id ? 'selected' : '' }}>
                        {{ $subsidiary->name }}
                    </option>
                    @endforeach
                </select>
                <small class="text-danger" x-text="errors.subsidiary_id"></small>
                @error('subsidiary_id')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="division" class="form-label fw-bold small">Divisi <span class="text-danger">*</span></label>
                <input type="text" id="division" name="division" x-ref="division" value="{{ old('division') }}"
                    class="form-control @error('division') is-invalid @enderror" placeholder="Nama Divisi">
                <small class="text-danger" x-text="errors.division"></small>
                @error('division')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small" for="date">Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="date" name="date" x-ref="date"
                    @change="updatePaymentNumber"
                    value="{{ old('date', date('Y-m-d')) }}"
                    class="form-control @error('date') is-invalid @enderror">
                <small class="text-danger" x-text="errors.date"></small>
                @error('date')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small" for="payment_number">Nomor Payment <span class="text-danger">*</span></label>
                <input type="text" id="payment_number" name="payment_number" x-ref="payment_number"
                    x-model="paymentNumber"
                    class="form-control bg-light @error('payment_number') is-invalid @enderror" readonly placeholder="Terisi Otomatis">
                <small class="text-danger" x-text="errors.payment_number"></small>
                @error('payment_number')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <hr class="my-4">

        {{-- SECTION 2: DYNAMIC ITEMS --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-list-task me-2"></i>Detail Pembayaran</h5>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" @click="addItem">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
            </button>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center fw-bold small">
                    <tr>
                        <th width="30%">Deskripsi <span class="text-danger">*</span></th>
                        <th width="10%">QTY <span class="text-danger">*</span></th>
                        <th width="10%">Satuan <span class="text-danger">*</span></th>
                        <th width="18%">Harga Satuan <span class="text-danger">*</span></th>
                        <th width="18%">Harga Total</th>
                        <th width="14%">Due Date</th>
                        <th width="5%">Menu</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td>
                                <input type="text" class="form-control form-control-sm" placeholder="Nama Item"
                                    :name="`items[${index}][item_name]`" x-model="item.item_name">
                                <small class="text-danger d-block" x-text="errors[`item_name_${index}`]"></small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-center" placeholder="Qty"
                                    :name="`items[${index}][quantity]`" x-model.number="item.quantity">
                                <small class="text-danger d-block" x-text="errors[`quantity_${index}`]"></small>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm text-center" placeholder="Satuan"
                                    :name="`items[${index}][unit]`" x-model="item.unit">
                                <small class="text-danger d-block" x-text="errors[`unit_${index}`]"></small>
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control form-control-sm text-end" placeholder="Harga Satuan"
                                    :name="`items[${index}][unit_price]`" x-model.number="item.unit_price">
                                <small class="text-danger d-block" x-text="errors[`unit_price_${index}`]"></small>
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control form-control-sm text-end bg-light"
                                    :name="`items[${index}][amount]`" :value="(item.quantity * item.unit_price).toFixed(2)" readonly>
                            </td>
                            <td>
                                <input type="date" class="form-control form-control-sm" :name="`items[${index}][due_date]`"
                                    x-model="item.due_date">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm border-0" @click="removeItem(index)"
                                    x-show="items.length > 1" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- SECTION 3: PURPOSE & ATTACHMENT --}}
        <div class="row g-3 mb-4 pt-3 border-top">
            <div class="col-md-8">
                <label for="purpose" class="form-label fw-bold small">Catatan / Note</label>
                <textarea name="purpose" rows="2" placeholder="Tulis catatan atau instruksi khusus..."
                    class="form-control @error('purpose') is-invalid @enderror">{{ old('purpose') }}</textarea>
                @error('purpose')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="attachment" class="form-label fw-bold small">Lampiran Utama</label>
                <input type="file" name="attachment" class="form-control shadow-sm @error('attachment') is-invalid @enderror">
                <small class="form-text text-muted">Contoh: Invoice, kwitansi, dll.</small>
                @error('attachment')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- SECTION 4: GRAND TOTAL & ACTION --}}
        <div class="row align-items-center border-top pt-3">
            <div class="col-md-6">
                <h4 class="fw-bold mb-0 text-dark">
                    Grand Total: <span class="text-primary" x-text="formatCurrency(grandTotal)"></span>
                </h4>
                <input type="hidden" name="grand_total" :value="grandTotal.toFixed(2)">
            </div>
            <div class="col-md-6 text-end">
                <button type="submit" class="btn btn-success px-5 fw-bold shadow">
                    SIMPAN REQUEST PAYMENT
                </button>
            </div>
        </div>
    </form>
    @endcomponent
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rpForm', () => {
            @php
            $defaultItems = [
                [
                    'item_name' => '',
                    'quantity' => 1,
                    'unit' => '',
                    'unit_price' => 0,
                    'amount' => 0,
                    'due_date' => '',
                ],
            ];
            @endphp

            const initialItems = @json(old('items', $defaultItems));

            return {
                paymentNumber: "{{ old('payment_number', $autoPaymentNumber ?? '') }}",
                items: initialItems.map(item => ({
                    item_name: item.item_name || '',
                    quantity: item.quantity || 1,
                    unit: item.unit || '',
                    unit_price: item.unit_price || 0,
                    amount: item.amount || 0,
                    due_date: item.due_date || ''
                })),
                errors: {},

                get grandTotal() {
                    return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_price || 0)), 0);
                },

                init() {
                    if (!this.paymentNumber && this.$refs.subsidiary_id?.value) {
                        this.updatePaymentNumber();
                    }
                },

                async updatePaymentNumber() {
                    const subsidiaryId = this.$refs.subsidiary_id.value;
                    const date = this.$refs.date.value;

                    if (!subsidiaryId) {
                        this.paymentNumber = '';
                        return;
                    }

                    try {
                        let response = await fetch(`{{ route('request-payment.get-next-number') }}?subsidiary_id=${subsidiaryId}&date=${date}`);
                        let data = await response.json();
                        this.paymentNumber = data.payment_number;
                    } catch (error) {
                        console.error('Gagal mengambil nomor payment:', error);
                    }
                },

                addItem() {
                    this.items.push({
                        item_name: '',
                        quantity: 1,
                        unit: '',
                        unit_price: 0,
                        amount: 0,
                        due_date: ''
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                formatCurrency(val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
                },

                submitForm(e) {
                    this.errors = {};

                    if (!this.$refs.subsidiary_id.value) this.errors.subsidiary_id = 'Pilih Plant!';
                    if (!this.$refs.division.value) this.errors.division = 'Divisi wajib diisi!';
                    if (!this.$refs.date.value) this.errors.date = 'Tanggal wajib diisi!';
                    if (!this.$refs.payment_number.value) this.errors.payment_number = 'Nomor wajib diisi!';

                    this.items.forEach((item, i) => {
                        if (!item.item_name) this.errors[`item_name_${i}`] = 'Wajib!';
                        if (!item.quantity || item.quantity <= 0) this.errors[`quantity_${i}`] = 'Min 1!';
                        if (!item.unit) this.errors[`unit_${i}`] = 'Wajib!';
                        if (!item.unit_price || item.unit_price <= 0) this.errors[`unit_price_${i}`] = 'Wajib!';
                    });

                    if (Object.keys(this.errors).length === 0) {
                        e.target.submit();
                    } else {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            };
        });
    });
</script>
@endpush