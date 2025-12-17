@extends('layouts.app')
@section('title', "Data $subsidiary->name")
@section('menuSubsidiaries', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Data Perusahaan
            @endslot
            <div class="btn-group d-flex gap-2 flex-wrap">
                @can('subsidiary.edit')
                    <x-buttons.edit href="{{ route('subsidiaries.edit', ['subsidiary' => $subsidiary->id]) }}"></x-buttons.edit>
                @endcan
                @can('subsidiary.delete')
                    <form action="{{ route('subsidiaries.destroy', ['subsidiary' => $subsidiary->id]) }}" id="hapus"
                        method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" form="hapus" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Delete"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                @endcan
            </div>
            <div class="row my-3 align-items-center text-center">
                <div class="col-md-3 mx-auto">
                    @php
                        $logoPath = $subsidiary->logo
                            ? asset("storage/subsidiary/logo/{$subsidiary->logo}")
                            : asset('storage/subsidiary/logo/default.png');
                    @endphp
                    <img class="img-thumbnail" src="{{ $logoPath }}" alt="Logo {{ $subsidiary->name }}" loading="lazy"
                        oncontextmenu="return false">
                </div>
                <div class="col-md-7 mx-auto">
                    <h3>{{ $subsidiary->name }}</h3>
                    <div>
                        {{ $subsidiary->tagline ?? '-' }}
                        | {{ $subsidiary->npwp ?? '-' }}
                        | {{ $subsidiary->email ?? '-' }}
                        | {{ $subsidiary->phone ?? '-' }}
                        | {{ $subsidiary->address ?? '-' }}
                    </div>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover" id="table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA KARYAWAN</th>
                            <th>POSISI</th>
                            <th>STATUS PEGAWAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subsidiary->employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><a href="{{ route('employees.show', $employee->id) }}" class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">
                                        {{ $employee->nama }}
                                    </a></td>
                                <td>{{ $employee->posisi }}</td>
                                <td>{{ $employee->status_peg }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>

@endsection
