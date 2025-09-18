@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('menuEmployees', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                LOG ACTIVITY
            @endslot

            <x-buttons.delete class="d-flex mb-3" data-bs-toggle="modal" data-bs-target="#deleteModal">

            </x-buttons.delete>
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Hapus Log</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('log.activity.truncate') }}" id="deleteForm">
                                Apakah Anda yakin ingin menghapus semua log activity?
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger" form="deleteForm">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="table">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>IP</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Waktu</th>
                            <th>Method</th>
                            <th width="500px">Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $key => $log)
                            <tr>
                                <td class="text-success">{{ $log->url }}</td>
                                <td class="text-warning">{{ $log->ip }}</td>
                                <td>{{ $log->username }}</td>
                                <td>{{ $log->role }}</td>
                                <td>{{ $log->created_at }}</td>
                                <td><label class="label label-info">{{ $log->method }}</label></td>
                                <td class="text-danger">{{ $log->agent }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
    </div>


@endsection
