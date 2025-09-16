@extends('layouts.app')
@section('title', 'Daftar Perusahaan')
@section('menuSubsidiaries', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                🏢 DATA PERUSAHAAN
            @endslot

            {{-- Tombol Create --}}
            <div class="mb-3 d-flex justify-content-end">
                @can('create', App\Models\Subsidiary::class)
                    <x-buttons.create href="{{ route('subsidiaries.create') }}" />
                @endcan
            </div>

            {{-- Tabel Subsidiary --}}
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Karyawan</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subsidiaries as $subsidiary)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @can('update', $subsidiary)
                                        <a href="{{ route('subsidiaries.show', $subsidiary->id) }}" class="text-decoration-none"
                                            data-bs-toggle="tooltip" title="Lihat detail">
                                            {{ $subsidiary->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ $subsidiary->name }}</span>
                                    @endcan
                                </td>
                                <td class="text-center">{{ $subsidiary->employees_count ?? 0 }} Orang</td>
                                <td>{{ $subsidiary->address ?: 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada perusahaan terdaftar</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
