@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('menuEmployees', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between flex-wrap">
                @can('create', App\Models\Employee::class)
                    <div class="d-flex gap-2 flex-wrap">
                        <x-buttons.create href="{{ route('employees.create') }}" wire:navigate></x-buttons.create>
                        <x-buttons.excel href="{{ route('employees.excel') }}"></x-buttons.excel>
                    </div>
                @endcan
                <x-buttons.pdf href="{{ route('employees.pdf') }}"></x-buttons.pdf>
            </div>
            @slot('header')
                DATA KARYAWAN
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PERUSAHAAN</th>
                            <th>NIP</th>
                            <th>NAMA</th>
                            <th>JABATAN</th>
                            <th>SEKSI</th>
                            <th>DEPARTEMEN</th>
                            <th>STATUS</th>
                            <th>SISA KONTRAK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <th>{{ $employees->firstItem() + $loop->iteration - 1 }}</th>
                                <td>{{ $employee->subsidiary->name }}</td>
                                <td> {{ $employee->nip }}</td>
                                <td><a href="{{ route('employees.show', $employee->id) }}" class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail" wire:navigate>
                                        {{ $employee->nama }}
                                    </a></td>
                                <td>{{ $employee->posisi }}</td>
                                <td>{{ $employee->seksi }}</td>
                                <td>{{ $employee->departemen }}</td>
                                <td>{{ $employee->status_peg }}</td>
                                <td>
                                    @if ($employee->status_peg == 'PKWT')
                                        @php
                                            $days = \Carbon\Carbon::now()->diffInDays($employee->akhir_kontrak, false);
                                        @endphp

                                        @if ($days < 0)
                                            <span class="text-danger">sudah berakhir</span>
                                        @else
                                            <span class="text-success">{{ $days }} hari</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <td colspan="9" class="text-center">Tidak ada data...</td>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PERUSAHAAN</th>
                                <th>NIP</th>
                                <th>NAMA</th>
                                <th>JABATAN</th>
                                <th>SEKSI</th>
                                <th>DEPARTEMEN</th>
                                <th>STATUS</th>
                                <th>SISA KONTRAK</th>
                            </tr>
                        </thead>
                    </tfoot>
                </table>
            </div>
        @endcomponent

    @endsection
