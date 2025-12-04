@extends('layouts.app')
@section('title', 'Buat RO')
@section('menuRO', 'active')
@section('content')
    <div class="container mt-3" x-data="{
        items: {{ json_encode(old('items', [['item_name' => '', 'quantity' => 1, 'unit' => '']])) }},
        errors: {}
    }">

        @component('components.card')
            @slot('header')
                Create
            @endslot

            <form action="{{ route('request-order.store') }}" method="POST"
                @submit.prevent="
                errors = {};
                if (!$refs.division.value) errors.division = 'Divisi wajib diisi';
                if (!$refs.request_date.value) errors.request_date = 'Tanggal wajib diisi';
                if (!$refs.request_number.value) errors.request_number = 'Nomor wajib diisi';
                if (!$refs.purpose.value) errors.purpose = 'Tujuan wajib diisi';

                items.forEach((item, i) => {
                    if (!item.item_name) errors[`item_name_${i}`] = 'Nama barang wajib diisi';
                    if (!item.quantity || item.quantity <= 0) errors[`quantity_${i}`] = 'Qty harus lebih dari 0';
                    if (!item.unit) errors[`unit_${i}`] = 'Satuan wajib diisi';
                });

                if (Object.keys(errors).length === 0) $el.submit();
              ">
                @csrf

                {{-- Data umum --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="division" class="form-label">Plant</label>
                        <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                            id="subsidiary_id">
                            <option value="" {{ old('subsidiary_id') == '' ? 'selected' : '' }}>Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option
                                    value="{{ $subsidiary->id }}"{{ old('subsidiary_id') == $subsidiary->id ? 'selected' : '' }}>
                                    {{ $subsidiary->name }}</option>
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
                        <label class="form-label" for="request_date">Tanggal</label>
                        <input type="date" id="request_date" name="request_date" x-ref="request_date"
                            value="{{ old('request_date') }}" class="form-control @error('request_date') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.request_date"></small>
                        @error('request_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="request_number">Nomor</label>
                        <input type="text" id="request_number" name="request_number" x-ref="request_number"
                            value="{{ old('request_number') }}"
                            class="form-control @error('request_number') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.request_number"></small>
                        @error('request_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Barang dinamis --}}
                <h5 class="fw-semibold mb-3">Daftar Barang</h5>
                <template x-for="(item, index) in items" :key="index">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Nama Barang"
                                :name="`items[${index}][item_name]`" x-model="item.item_name">
                            <small class="text-danger" x-text="errors[`item_name_${index}`]"></small>
                            @error('items.*.item_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" placeholder="QTY" :name="`items[${index}][quantity]`"
                                x-model="item.quantity">
                            <small class="text-danger" x-text="errors[`quantity_${index}`]"></small>
                            @error('items.*.quantity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" placeholder="Satuan" :name="`items[${index}][unit]`"
                                x-model="item.unit">
                            <small class="text-danger" x-text="errors[`unit_${index}`]"></small>
                            @error('items.*.unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-2 text-center">
                            <button type="button" class="btn btn-danger btn-sm" @click="items.splice(index,1)"
                                x-show="items.length > 1">Hapus</button>
                        </div>
                    </div>
                </template>

                <button type="button" class="btn btn-success mb-3"
                    @click="items.push({ item_name: '', quantity: 1, unit: '' })">
                    + Tambah Barang
                </button>

                {{-- Purpose --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="purpose" class="form-label">Tujuan</label>
                        <input type="text" name="purpose" id="purpose" x-ref="purpose" value="{{ old('purpose') }}"
                            placeholder="Purpose" class="form-control @error('purpose') is-invalid @enderror">
                        <small class="text-danger" x-text="errors.purpose"></small>
                        @error('purpose')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
