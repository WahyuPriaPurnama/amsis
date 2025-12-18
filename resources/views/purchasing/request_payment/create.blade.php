@extends('layouts.app')
@section('title', 'Buat Request Payment')
@section('menuPayment', 'active')
@section('content')
    <div class="container mt-3" x-data="{
        items: {{ json_encode(old('items', [['item_name' => '', 'quantity' => 1, 'unit' => '', 'unit_price' => 0, 'amount' => 0, 'remark' => '']])) }},
        errors: {},
        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        }
    }">

        @component('components.card')
            @slot('header')
                Create Request Payment
            @endslot

            <form action="{{ route('request-payment.store') }}" method="POST" enctype="multipart/form-data"
                @submit.prevent="
                errors = {};
                if (!$refs.division.value) errors.division = 'Divisi wajib diisi';
                if (!$refs.date.value) errors.date = 'Tanggal wajib diisi';
                if (!$refs.payment_number.value) errors.payment_number = 'Nomor wajib diisi';

                items.forEach((item, i) => {
                    if (!item.item_name) errors[`item_name_${i}`] = 'Nama barang wajib diisi';
                    if (!item.quantity || item.quantity <= 0) errors[`quantity_${i}`] = 'Qty harus lebih dari 0';
                    if (!item.unit) errors[`unit_${i}`] = 'Satuan wajib diisi';
                    if (!item.unit_price || item.unit_price <= 0) errors[`unit_price_${i}`] = 'Harga wajib diisi';
                });

                if (Object.keys(errors).length === 0) $el.submit();
              ">
                @csrf

                {{-- Data umum --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="subsidiary_id" class="form-label">Plant</label>
                        <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                            id="subsidiary_id">
                            <option value="" {{ old('subsidiary_id') == '' ? 'selected' : '' }}>Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option value="{{ $subsidiary->id }}"
                                    {{ old('subsidiary_id') == $subsidiary->id ? 'selected' : '' }}>
                                    {{ $subsidiary->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subsidiary_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="division" class="form-label">Divisi</label>
                        <input type="text" id="division" name="division" x-ref="division" value="{{ old('division') }}"
                            class="form-control @error('division') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.division"></small>
                        @error('division')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="date">Tanggal</label>
                        <input type="date" id="date" name="date" x-ref="date" value="{{ old('date') }}"
                            class="form-control @error('date') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.date"></small>
                        @error('date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="payment_number">Nomor</label>
                        <input type="text" id="payment_number" name="payment_number" x-ref="payment_number"
                            value="{{ old('payment_number') }}"
                            class="form-control @error('payment_number') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.payment_number"></small>
                        @error('payment_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Barang dinamis --}}
                <h5 class="fw-semibold mb-3">Detail Pembayaran</h5>
                <template x-for="(item, index) in items" :key="index">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Nama Item"
                                :name="`items[${index}][item_name]`" x-model="item.item_name">
                            <small class="text-danger" x-text="errors[`item_name_${index}`]"></small>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" placeholder="Qty" :name="`items[${index}][quantity]`"
                                x-model.number="item.quantity">
                            <small class="text-danger" x-text="errors[`quantity_${index}`]"></small>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" placeholder="Satuan" :name="`items[${index}][unit]`"
                                x-model="item.unit">
                            <small class="text-danger" x-text="errors[`unit_${index}`]"></small>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" placeholder="Harga Satuan"
                                :name="`items[${index}][unit_price]`" x-model.number="item.unit_price">
                            <small class="text-danger" x-text="errors[`unit_price_${index}`]"></small>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" placeholder="Total"
                                :name="`items[${index}][amount]`" :value="(item.quantity * item.unit_price).toFixed(2)"
                                readonly>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-danger btn-sm" @click="items.splice(index,1)"
                                x-show="items.length > 1">Hapus</button>
                        </div>
                    </div>
                </template>

                <button type="button" class="btn btn-success mb-3"
                    @click="items.push({ item_name: '', quantity: 1, unit: '', unit_price: 0, amount: 0 })">
                    + Tambah Item
                </button>

                {{-- Purpose --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="purpose" class="form-label">Tujuan Pembayaran</label>
                        <input type="text" name="purpose" value="{{ old('purpose') }}"
                            placeholder="Tujuan pembayaran..." class="form-control @error('purpose') is-invalid @enderror">
                        @error('purpose')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Attachment --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="attachment" class="form-label">Lampiran</label>
                        <input type="file" name="attachment"
                            class="form-control @error('attachment') is-invalid @enderror">
                        @error('attachment')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Grand Total --}}
                <div class="row mb-3">
                    <div class="col-12 text-end">
                        <h5>Total: <span x-text="grandTotal.toFixed(2)"></span></h5>
                        <input type="hidden" name="grand_total" :value="grandTotal.toFixed(2)">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
