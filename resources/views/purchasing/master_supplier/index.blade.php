@extends('layouts.app')
@section('title', 'Master Supplier')
@section('menuSupplier', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users me-2"></i> Master Supplier</span>
                    <a href="{{ route('master-supplier.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Supplier
                    </a>
                </div>
            @endslot

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Supplier</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><a href="{{ route('master-supplier.show', $supplier->id) }}" class="text-decoration-none">{{ $supplier->name }}</a></td>
                                <td>{{ $supplier->contact }}</td>
                                <td>{{ $supplier->address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
