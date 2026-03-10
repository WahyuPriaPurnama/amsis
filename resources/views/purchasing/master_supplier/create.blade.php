@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('menuSupplier', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-plus me-2"></i> Tambah Supplier Baru</span>
                    <a href="{{ route('master-supplier.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            @endslot

            {{-- Alert Global (Opsional) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('master-supplier.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Informasi Dasar --}}
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Kode Supplier</label>
                        <input type="text" name="code" id="code"
                            class="form-control @error('code') is-invalid @enderror" placeholder="Contoh: SUP-001"
                            value="{{ old('code') }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nama Supplier</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Tipe Supplier</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="Lokal" {{ old('type') == 'Lokal' ? 'selected' : '' }}>Lokal</option>
                            <option value="Import" {{ old('type') == 'Import' ? 'selected' : '' }}>Import</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="contact_person" class="form-label">Contact Person (PIC)</label>
                        <input type="text" name="contact_person" id="contact_person"
                            class="form-control @error('contact_person') is-invalid @enderror"
                            value="{{ old('contact_person') }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kontak & Alamat --}}
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">No. Telepon</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-3">
                    <h6 class="mb-3 text-muted">Informasi Finansial & Perpajakan</h6>

                    {{-- Finansial --}}
                    <div class="col-md-4 mb-3">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="text" name="npwp" id="npwp"
                            class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp') }}">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="bank_name" class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name"
                            class="form-control @error('bank_name') is-invalid @enderror" placeholder="Contoh: BCA / Mandiri"
                            value="{{ old('bank_name') }}">
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="bank_account_number" class="form-label">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" id="bank_account_number"
                            class="form-control @error('bank_account_number') is-invalid @enderror"
                            value="{{ old('bank_account_number') }}">
                        @error('bank_account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="term_of_payment" class="form-label">Term of Payment (Hari)</label>
                        <div class="input-group">
                            <input type="number" name="term_of_payment" id="term_of_payment"
                                class="form-control @error('term_of_payment') is-invalid @enderror" placeholder="30"
                                value="{{ old('term_of_payment', 0) }}">
                            <span class="input-group-text">Hari</span>
                            @error('term_of_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 shadow-sm p-3 bg-light rounded d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Supplier
                    </button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
