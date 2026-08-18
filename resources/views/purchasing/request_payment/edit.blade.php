@extends('layouts.app')
@section('title', 'Edit Request Payment')
@section('menuPayment', 'active')

@section('content')
    <div class="container mt-3" x-data="{
        items: {{ json_encode(old('items', $payment->items ?? [['item_name' => '', 'quantity' => 1, 'unit' => '', 'unit_price' => 0, 'amount' => 0, 'remark' => '', 'due_date' => '']])) }},
        errors: {},
        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        }
    }">

        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span>Edit Request Payment</span>
                    <span class="badge bg-secondary">{{ $payment->payment_number }}</span>
                </div>
            @endslot

            <form action="{{ route('request-payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data"
                @submit.prevent="
                errors = {};
                if (!$refs.division.value) errors.division = 'Divisi wajib diisi';
                if (!$refs.date.value) errors.date = 'Tanggal wajib diisi';

                items.forEach((item, i) => {
                    if (!item.item_name) errors[`item_name_${i}`] = 'Nama barang wajib diisi';
                    if (!item.quantity || item.quantity <= 0) errors[`quantity_${i}`] = 'Qty harus lebih dari 0';
                    if (!item.unit) errors[`unit_${i}`] = 'Satuan wajib diisi';
                    if (!item.unit_price || item.unit_price <= 0) errors[`unit_price_${i}`] = 'Harga wajib diisi';
                });

                if (Object.keys(errors).length === 0) $el.submit();
              ">
                @csrf
                @method('PUT')

                {{-- Hidden input jika payment_number dibutuhkan di backend tetapi tidak diedit --}}
                <input type="hidden" name="payment_number" value="{{ $payment->payment_number }}">

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row mb-3 mt-2">
                    <div class="col-md-3">
                        <label for="subsidiary_id" class="form-label">Plant</label>
                        <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                            id="subsidiary_id">
                            <option value="">Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option value="{{ $subsidiary->id }}"
                                    {{ old('subsidiary_id', $payment->subsidiary_id) == $subsidiary->id ? 'selected' : '' }}>
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
                        <input type="text" id="division" name="division" x-ref="division"
                            value="{{ old('division', $payment->division) }}"
                            class="form-control @error('division') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.division"></small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="date">Tanggal</label>
                        <input type="date" id="date" name="date" x-ref="date"
                            value="{{ old('date', \Carbon\Carbon::parse($payment->date)->format('Y-m-d')) }}"
                            class="form-control @error('date') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.date"></small>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Detail Pembayaran</h5>

                <div class="row fw-semibold mb-2">
                    <div class="col-md-4">Deskripsi</div>
                    <div class="col-md-1">QTY</div>
                    <div class="col-md-1">Satuan</div>
                    <div class="col-md-2">Harga Satuan</div>
                    <div class="col-md-2">Harga Total</div>
                    <div class="col-md-1">Due Date</div>
                    <div class="col-md-1 text-center">Menu</div>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="row mb-2">
                        <input type="hidden" :name="`items[${index}][id]`" :value="item.id || ''">

                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Nama Item"
                                :name="`items[${index}][item_name]`" x-model="item.item_name">
                            <small class="text-danger" x-text="errors[`item_name_${index}`]"></small>
                        </div>
                        <div class="col-md-1">
                            <input type="number" step="0.01" class="form-control" placeholder="Qty"
                                :name="`items[${index}][quantity]`" x-model.number="item.quantity">
                            <small class="text-danger" x-text="errors[`quantity_${index}`]"></small>
                        </div>
                        <div class="col-md-1">
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
                        <div class="col-md-1">
                            <input type="date" class="form-control" :name="`items[${index}][due_date]`"
                                x-model="item.due_date">
                            <small class="text-danger" x-text="errors[`due_date_${index}`]"></small>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" @click="items.splice(index,1)"
                                x-show="items.length > 1"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </template>

                <button type="button" class="btn btn-outline-success btn-sm mb-4 mt-2"
                    @click="items.push({ id: '', item_name: '', quantity: 1, unit: '', unit_price: 0, amount: 0, due_date: '' })">
                    + Tambah Baris
                </button>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="purpose" class="form-label">Note / Keperluan:</label>
                        <input type="text" name="purpose" value="{{ old('purpose', $payment->purpose) }}"
                            placeholder="Note..." class="form-control @error('purpose') is-invalid @enderror">
                        @error('purpose')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="attachment" class="form-label">Lampiran:</label>
                        <input type="file" name="attachment"
                            class="form-control @error('attachment') is-invalid @enderror">

                        @if ($payment->attachment)
                            <small class="form-text mt-1 d-block">
                                File saat ini: <a href="{{ asset('storage/' . $payment->attachment) }}" target="_blank">Lihat
                                    Lampiran</a>
                            </small>
                        @endif
                        <small class="form-text text-muted">
                            Biarkan kosong jika tidak ingin mengubah dokumen pendukung.
                        </small>
                        @error('attachment')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12 text-end border-top pt-3">
                        <h5 class="fw-bold">Grand Total: <span class="text-primary" x-text="grandTotal.toFixed(2)"></span>
                        </h5>
                        <input type="hidden" name="grand_total" :value="grandTotal.toFixed(2)">
                    </div>
                </div>

                <div class="text-end d-flex justify-content-between">
                    <a href="{{ route('request-payment.index') }}" class="btn btn-secondary px-4">Kembali</a>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection