@extends('layouts.app')
@section('title', 'Detail Request Order')
@section('menuOrder', 'active')

@section('content')
<div class="container mt-3">
    @component('components.card')
    @slot('header')
    <div class="d-flex justify-content-between align-items-center">
        <span><i class="bi bi-info-circle me-2"></i>Detail Request Order</span>
        <span class="small">{{ $order->request_number }}</span>
    </div>
    @endslot

    <div class="row mt-3">
        <div class="col-md-8">
            @php
            $statusConfig = [
            'pending' => [
            'bg' => 'warning',
            'text' => 'dark',
            'label' => 'Menunggu Approval Kepala Divisi',
            ],
            'approved_by_div_head' => [
            'bg' => 'warning',
            'text' => 'dark',
            'label' => 'Menunggu Approval Plant Manager',
            ],
            'approved_by_manager' =>
            $order->subsidiary_id == 2
            ? ['bg' => 'success', 'text' => 'white', 'label' => 'Approved']
            : ['bg' => 'warning', 'text' => 'dark', 'label' => 'Menunggu Approval BOD'],
            'approved_by_bod' => ['bg' => 'success', 'text' => 'white', 'label' => 'Approved'],
            'completed' => [
            'bg' => 'primary',
            'text' => 'white',
            'label' => 'Selesai (Barang Diterima)',
            ],
            'partial' => ['bg' => 'info', 'text' => 'dark', 'label' => 'Diterima Sebagian'],
            ];
            $current = $statusConfig[$order->status] ?? [
            'bg' => 'secondary',
            'text' => 'white',
            'label' => $order->status,
            ];
            @endphp
            <div class="badge bg-{{ $current['bg'] }} text-{{ $current['text'] }} p-2 fs-6 w-100 shadow-sm">
                {{ strtoupper($current['label']) }}
            </div>
        </div>
        <div class="col-md-4 mt-2 mt-md-0 d-flex justify-content-md-end gap-1">
            {{-- Tombol Edit --}}
            @can('request-order.edit')
            <x-buttons.edit href="{{ route('request-order.edit', $order->id) }}"></x-buttons.edit>
            @endcan

            {{-- Tombol Delete --}}
            @can('request-order.delete')
            @if ($order->status === 'pending')
            <x-buttons.delete2 href="{{ route('request-order.destroy', $order->id) }}"></x-buttons.delete2>
            @endif
            @endcan
        </div>
    </div>

    {{-- Info Umum --}}
    <div class="row g-3 p-3 bg-light rounded border my-4">
        <div class="col-6 col-md-3">
            <label class="text-muted small d-block">Plant</label>
            <span class="fw-bold">{{ $order->subsidiary->name ?? '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
            <label class="text-muted small d-block">Divisi</label>
            <span class="fw-bold">{{ $order->division }}</span>
        </div>
        <div class="col-6 col-md-3">
            <label class="text-muted small d-block">Tanggal Request</label>
            <span class="fw-bold">{{ \Carbon\Carbon::parse($order->request_date)->format('d M Y') }}</span>
        </div>
        <div class="col-6 col-md-3">
            <label class="text-muted small d-block">Dibuat Oleh</label>
            <span class="fw-bold">{{ $order->requester->name ?? '-' }}</span>
        </div>
        {{-- Badges / Info Revisi --}}
        @if ($order->revision_count > 0)
        <div class="alert alert-info py-2 px-3 mt-3 d-flex align-items-center mb-0" role="alert">
            <i class="bi bi-pencil-square me-2 fs-5"></i>
            <div>
                <strong>Dokumen telah direvisi {{ $order->revision_count }}x</strong>
                <span class="text-muted small ms-2">
                    (Terakhir diperbarui: {{ \Carbon\Carbon::parse($order->last_revised_at)->format('d M Y, H:i') }} WIB)
                </span>
            </div>
        </div>
        @endif
    </div>

    {{-- Tabel Daftar Barang --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Daftar Barang & Realisasi</h5>

        {{-- TOMBOL RECEIVE: Muncul jika status sudah Approved atau Partial --}}
        @if (in_array($order->status, ['approved_by_manager', 'approved_by_bod', 'partial']))
        @can('request-order.receive')
        <a href="{{ route('request-order.receive', $order->id) }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-truck me-1"></i> Update Penerimaan
        </a>
        @endcan
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover border">
            <thead class="table-dark">
                <tr>
                    <th width="50">No.</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty Req</th>
                    <th class="text-center">Qty Rec</th>
                    <th>Satuan</th>
                    <th class="text-center" width="150">Realisasi</th>
                    <th>Info PO</th>
                    <th class="text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                @php
                $percentage = $item->quantity > 0 ? round(($item->qty_received / $item->quantity) * 100) : 0;

                $barColor = 'bg-danger';
                if ($percentage >= 100) {
                $barColor = 'bg-success';
                } elseif ($percentage > 0) {
                $barColor = 'bg-info';
                }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <span class="fw-bold">{{ $item->item_name }}</span>
                        @if ($item->remark)
                        <br><small class="text-muted">{{ $item->remark }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->qty_received >= $item->quantity ? 'bg-success' : ($item->qty_received > 0 ? 'bg-info text-dark' : 'bg-secondary') }}">
                            {{ $item->qty_received ?? 0 }}
                        </span>
                    </td>
                    <td>{{ $item->unit }}</td>

                    <td>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="fw-bold {{ $percentage >= 100 ? 'text-success' : 'text-muted' }}">
                                {{ $percentage }}%
                            </small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $barColor }}" role="progressbar"
                                style="width: {{ min($percentage, 100) }}%" aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <div class="small text-muted">
                            {{ $item->date_received ? \Carbon\Carbon::parse($item->date_received)->format('d/m/Y') : '-' }}
                        </div>
                    </td>

                    <td>
                        @if ($item->po_number)
                        <div class="small fw-bold text-dark">{{ $item->po_number }}</div>
                        <div class="small text-muted">
                            {{ $item->po_date ? \Carbon\Carbon::parse($item->po_date)->format('d/m/Y') : '-' }}
                        </div>
                        @else
                        <span class="text-muted small italic">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item->receipt_attachment)
                        <a href="{{ asset('storage/' . $item->receipt_attachment) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary p-1 py-0">
                            <i class="bi bi-file-earmark-image"></i>
                        </a>
                        @else
                        <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Note & Attachment --}}
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="p-3 border rounded h-100 shadow-sm">
                <label class="fw-bold text-muted small uppercase">Catatan / Keperluan:</label>
                <p class="mb-0 italic">{{ $order->purpose ?? 'Tidak ada catatan.' }}</p>
            </div>
        </div>
        <div class="col-md-4 mt-3 mt-md-0 text-center">
            <div class="p-3 border rounded h-100 shadow-sm">
                <label class="fw-bold text-muted small d-block mb-2">Lampiran RO (Header):</label>
                @if ($order->attachment)
                <a href="{{ asset('storage/' . $order->attachment) }}" target="_blank"
                    class="btn btn-outline-primary w-100">
                    <i class="bi bi-paperclip me-2"></i>Lihat Lampiran
                </a>
                @else
                <span class="text-muted small italic">Tidak ada lampiran</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer / Approval Tracking --}}
    <div class="mt-4 p-3 border-top bg-light rounded shadow-sm">
        <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
            <i class="bi bi-clock-history me-2"></i>Approval Tracking
        </h6>
        <div class="row text-center g-3 small">
            {{-- Approval Kepala Divisi --}}
            <div class="col-md-4 border-start">
                <span class="text-muted d-block small uppercase fw-bold">Div Head Approval</span>
                <span class="fw-bold {{ $order->divHead ? 'text-success' : 'text-muted' }}">
                    {{ $order->divHead->name ?? 'Waiting...' }}
                </span>
                @if ($order->approved_by_divhead_at)
                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar-check text-success me-1"></i>
                    {{ \Carbon\Carbon::parse($order->approved_by_divhead_at)->format('d M Y, H:i') }}
                </div>
                @endif
            </div>

            {{-- Approval Plant Manager --}}
            <div class="col-md-4 border-start">
                <span class="text-muted d-block small uppercase fw-bold">Plant Manager Approval</span>
                <span class="fw-bold {{ $order->plantManager ? 'text-success' : 'text-muted' }}">
                    {{ $order->plantManager->name ?? 'Waiting...' }}
                </span>
                @if ($order->approved_by_manager_at)
                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar-check text-success me-1"></i>
                    {{ \Carbon\Carbon::parse($order->approved_by_manager_at)->format('d M Y, H:i') }}
                </div>
                @endif
            </div>

            {{-- Approval BOD --}}
            <div class="col-md-4 border-start border-end">
                <span class="text-muted d-block small uppercase fw-bold">BOD Approval</span>
                <span class="fw-bold {{ $order->bod ? 'text-success' : 'text-muted' }}">
                    {{ $order->bod->name ?? 'Waiting...' }}
                </span>
                @if ($order->approved_by_bod_at)
                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar-check text-success me-1"></i>
                    {{ \Carbon\Carbon::parse($order->approved_by_bod_at)->format('d M Y, H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Bawah --}}
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('request-order.index') }}" class="btn btn-secondary px-4">Kembali</a>

        <div class="d-flex gap-2 align-items-center">
            {{-- Pengecekan Akses Tombol Unapprove --}}
            @php
            $canUnapprove = false;
            $user = auth()->user();

            if ($order->status === 'approved_by_div_head' && ($user->can('request-order.approve-division') || $user->can('request-order.unapprove') || $user->hasRole('super-admin'))) {
            $canUnapprove = true;
            } elseif ($order->status === 'approved_by_manager' && ($user->can('request-order.approve-manager') || $user->can('request-order.unapprove') || $user->hasRole('super-admin'))) {
            $canUnapprove = true;
            } elseif ($order->status === 'approved_by_bod' && ($user->can('request-order.approve-bod') || $user->can('request-order.unapprove') || $user->hasRole('super-admin'))) {
            $canUnapprove = true;
            }
            @endphp

            @if ($canUnapprove)
            <form action="{{ route('request-order.unapprove', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan persetujuan ini?')">
                @csrf
                <input type="hidden" name="from" value="show">
                <button type="submit" class="btn btn-warning" title="Batal Approve">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Unapprove
                </button>
            </form>
            @endif

            {{-- Tombol Cetak PDF --}}
            @if ($order->status === 'approved_by_bod' || ($order->subsidiary_id == 2 && $order->status === 'approved_by_manager'))
            <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
            @endif

            {{-- Logic Approval Forms --}}
            @php
            $approveRoute = null;
            $btnLabel = '';

            if ($order->status === 'pending' && ($user->can('request-order.approve-division') || $user->hasRole('super-admin'))) {
            $approveRoute = route('request-order.approve_div_head', ['id' => $order->id, 'from' => 'show']);
            $btnLabel = 'Approve Kadiv';
            } elseif ($order->status === 'approved_by_div_head' && ($user->can('request-order.approve-manager') || $user->hasRole('super-admin'))) {
            $approveRoute = route('request-order.approve_manager', ['id' => $order->id, 'from' => 'show']);
            $btnLabel = 'Approve Manager';
            } elseif ($order->status === 'approved_by_manager' && $order->subsidiary_id != 2 && ($user->can('request-order.approve-bod') || $user->hasRole('super-admin'))) {
            $approveRoute = route('request-order.approve_bod', ['id' => $order->id, 'from' => 'show']);
            $btnLabel = 'Approve BOD';
            }
            @endphp

            @if ($approveRoute)
            <form action="{{ $approveRoute }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui RO ini?')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2-circle me-1"></i> {{ $btnLabel }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @endcomponent
</div>
@endsection