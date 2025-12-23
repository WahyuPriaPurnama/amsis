@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('menuEmployees', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between">
                @unless (auth()->user()->hasRole('employee'))
                    <div class="d-flex gap-2 flex-wrap">
                        <x-buttons.create href="{{ route('employees.create') }}" />
                        <x-buttons.excel href="{{ route('employees.excel') }}">Export</x-buttons.excel>
                        <x-buttons.import data-bs-original-title="Import Excel" data-bs-toggle="modal"
                            data-bs-target="#importModal">Import</x-buttons.import>
                        <x-buttons.pdf href="{{ route('employees.pdf') }}" />
                    </div>

                    <form method="GET" action="{{ route('employees.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari nama, NIP atau NIK">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>
                @endunless
            </div>
            @slot('header')
                🧑‍💼DATA KARYAWAN
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display">
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
                                <td>{{ $employees->firstItem() + $loop->iteration - 1 }}</td>
                                <td>{{ $employee->subsidiary->name }}</td>
                                <td> {{ $employee->nip }}</td>
                                <td><a href="{{ route('employees.show', $employee->id) }}" class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">
                                        {{ $employee->nama }}
                                    </a></td>
                                <td>{{ $employee->posisi }}</td>
                                <td>{{ $employee->seksi }}</td>
                                <td>{{ $employee->departemen }}</td>
                                <td>{{ $employee->status_peg }}</td>
                                <td>
                                    @if ($employee->status_peg == 'PKWT')
                                        @php
                                            $akhirKontrak = Carbon\Carbon::parse(
                                                $employee->akhir_kontrak,
                                            )->startOfDay();
                                            $days = Carbon\Carbon::now()->diffInDays($akhirKontrak, false);
                                            $daysRaw = $days;
                                            $days = floor($daysRaw);

                                        @endphp

                                        @if ($days < 0)
                                            <span style="color: red;">{{ abs($days) }} hari yang
                                                lalu</span>
                                        @elseif ($days === 0)
                                            <span style="color: orange;">hari ini</span>
                                        @else
                                            <span class="text-success">
                                                {{ $days }} hari
                                            </span>
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
                    </tfoot>
                </table>
                {{ $employees->links() }}
            </div>
        @endcomponent
    </div>
    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih file Excel (.xlsx)</label>
                            <input type="file" name="file" id="file"
                                class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>


@endsection
