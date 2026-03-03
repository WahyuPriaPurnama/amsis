@component('components.card')
    @slot('header')
        Counter Shin I 10
    @endslot

    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <button class="btn btn-sm btn-outline-primary" onclick="resetToToday()">
            <i class="bi bi-calendar-check"></i> Hari Ini
        </button>

        <div class="input-group input-group-sm" style="width: auto;">
            <span class="input-group-text bg-light text-muted">Riwayat:</span>
            <input type="date" id="filterDate" class="form-control" value="{{ date('Y-m-d') }}"
                onchange="fetchRpmChartData('day')">
        </div>
    </div>

    <canvas id="rpmChart"></canvas>
@endcomponent
