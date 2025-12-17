@extends('layouts.app')
@section('title', 'Data Scanlog')
@section('menuScanlog', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex flex-wrap gap-1">
                <button type="button" class="btn btn-success w-sm-auto h-100" data-bs-toggle="modal" data-bs-target="#import"
                    data-placement="top" title="Import Data" id="importButton">
                    <i class="bi bi-file-earmark-arrow-down-fill">
                    </i>
                </button>
                <x-buttons.excel href="{{ route('scanlog.export') }}"></x-buttons.excel>
                <button type="submit" data-bs-toggle="tooltip" title="Bulatkan Jam" class="btn btn-success w-sm-auto h-100"
                    form="myForm"><i class="bi bi-clock-fill"></i>
                </button>
                <x-buttons.delete data-bs-toggle="modal" data-bs-target="#delete"></x-buttons.delete>

                <form action="{{ route('scanlog.convert') }}" id="myForm" method="post">
                    @csrf
                    <input type="hidden" name="jam" value="60">
                </form>
                @if (session('alert'))
                    <a href="{{ route('scanlog.proses.gaji') }}" class="btn btn-primary w-sm-auto h-100">Hitung Gaji</a>
                @else
                    <span class="text-muted">silahkan convert terlebih dahulu</span>
                @endif
                <div class="alert alert-warning small ms-auto">
                    <i class="bi bi-info-circle-fill"></i> Jam Efektif = dipotong istirahat 1 jam atau
                    1,5 jam
                    (jika >=10 jam)
                </div>
            </div>
            <div class="modal fade" aria-hidden="true" id="delete" tabindex="-1" role="dialog" data-bs-backdrop="static">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hapus Data Scanlog</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('scanlog.truncate') }}" id="deleteForm">
                                <p>Apakah Anda yakin ingin menghapus semua data scanlog?</p>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="bi bi-x-lg"></i></button>
                            <button type="submit" form="deleteForm" class="btn btn-danger">Yakin</button>
                        </div>
                    </div>
                </div>
            </div>
            @slot('header')
                Data Scanlog
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>PIN</th>
                            <th>NAMA</th>
                            <th>DEPT</th>
                            <th>TANGGAL</th>
                            <th>SHIFT</th>
                            <th>MASUK</th>
                            <th>PULANG</th>
                            <th><i class="bi bi-clock-fill"></i></th>
                            <th>GAJI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scanlogs as $scanlog)
                            <tr>
                                <td>
                                    @if (!$scanlog->harian)
                                        <span class="badge text-bg-danger">
                                            {{ $scanlog->pin }}</span>
                                    @else
                                        {{ $scanlog->pin }}
                                    @endif
                                </td>
                                <td>{{ $scanlog->nama }}</td>
                                <td>{{ $scanlog->dept }}</td>
                                <td>{{ $scanlog->tgl }}</td>
                                <td>
                                    @if (in_array($scanlog->jk, ['Tidak Hadir', 'Libur Rutin']) || !$scanlog->sm || !$scanlog->sp)
                                        <span class="badge text-bg-warning">
                                            {{ $scanlog->jk }}
                                        </span>
                                    @else
                                        {{ $scanlog->jk }}
                                    @endif
                                </td>
                                <td>{{ $scanlog->sm }}</td>
                                <td>{{ $scanlog->sp }}</td>
                                <td>{{ $scanlog->dk }}</td>
                                <td>{{ 'Rp' . number_format($scanlog->tgaji, 2, ',', '.') }}</td>
                                <td>
                                    @if ($scanlog->status == 0)
                                        <span class="badge text-bg-danger">
                                            <i class="bi bi-radioactive" data-bs-toggle="tooltip" title="belum diproses"></i>
                                        </span>
                                    @elseif($scanlog->status == 1 && $scanlog->harian)
                                        <span class="badge text-bg-success">
                                            <i class="bi bi-check-circle-fill" data-bs-toggle="tooltip" title="sudah diproses"></i>
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning">
                                            <i class="bi bi-exclamation-diamond-fill" data-bs-toggle="tooltip" title="scanlog tidak lengkap"></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endcomponent

            <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">IMPORT DATA</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form action="{{ route('scanlog.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>PILIH FILE</label>
                                    <input type="file" name="file"
                                        class="form-control @error('file') is-invalid @enderror" required>
                                    @error('file')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">TUTUP</button>
                                <button type="submit" class="btn btn-success">IMPORT</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div id="hapusToast" class="toast align-items-center text-bg-danger border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Data berhasil dihapus.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

    </div>

@endsection
