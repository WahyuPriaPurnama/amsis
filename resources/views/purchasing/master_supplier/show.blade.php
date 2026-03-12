@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('menuSupplier', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users me-2"></i> Detail Supplier</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('master-supplier.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="{{ route('master-supplier.edit', $supplier->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('master-supplier.destroy', $supplier->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endslot
            <div class="mb-3">
                <div class="row">
                    <div class="col col-md-3">
                        <label for="code" class="form-label">Kode Supplier</label>
                        <input type="text" id="code" class="form-control" value="{{ $supplier->code }}" disabled>
                    </div>
                    <div class="col col-md-6">
                        <label for="name" class="form-label">Nama Supplier</label>
                        <input type="text" id="name" class="form-control" value="{{ $supplier->name }}" disabled>
                    </div>
                    <div class="col col-md-3">
                        <label for="type" class="form-label">Jenis Supplier</label>
                        <input type="text" id="type" class="form-control" value="{{ $supplier->type }}" disabled>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea id="address" class="form-control" rows="3" disabled>{{ $supplier->address }}</textarea>
            </div>
            <div class="mb-3">
                <div class="row">
                    <div class="col col-md-4">
                        <label for="contact" class="form-label">Kontak</label>
                        <input type="text" id="contact" class="form-control" value="{{ $supplier->contact_person }}"
                            disabled>
                    </div>
                    <div class="col col-md-4">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="text" id="phone" class="form-control" value="{{ $supplier->phone }}" disabled>
                    </div>
                    <div class="col col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" value="{{ $supplier->email }}" disabled>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
@endsection
