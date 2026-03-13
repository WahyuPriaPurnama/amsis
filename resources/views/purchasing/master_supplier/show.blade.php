@extends('layouts.app')
@section('title', 'Detail Supplier') {{-- Judul diperbaiki --}}
@section('menuSupplier', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-id-card me-2"></i> Detail Supplier</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('master-supplier.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>

                        @can('master-supplier.edit')
                            <a href="{{ route('master-supplier.edit', $supplier->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endcan

                        @can('master-supplier.delete')
                            <form action="{{ route('master-supplier.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endslot

            <div class="mb-4">
                <div class="row g-3"> {{-- Menggunakan g-3 untuk gap antar kolom yang konsisten --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Kode Supplier</label>
                        <p class="form-control-plaintext border-bottom pb-2">{{ $supplier->code }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nama Supplier</label>
                        <p class="form-control-plaintext border-bottom pb-2">{{ $supplier->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Jenis Supplier</label>
                        <p class="form-control-plaintext border-bottom pb-2">
                            <span class="badge bg-info text-dark">{{ $supplier->type }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-muted text-uppercase">Alamat</label>
                <p class="form-control-plaintext border-bottom pb-2">{{ $supplier->address ?: '-' }}</p>
            </div>

            <div class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Kontak Person (CP)</label>
                        <p class="form-control-plaintext border-bottom pb-2">{{ $supplier->contact_person ?: '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Telepon</label>
                        <p class="form-control-plaintext border-bottom pb-2">
                            <i class="fas fa-phone-alt me-1 text-secondary small"></i> {{ $supplier->phone }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Email</label>
                        <p class="form-control-plaintext border-bottom pb-2">
                            <i class="fas fa-envelope me-1 text-secondary small"></i> {{ $supplier->email ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
@endsection
