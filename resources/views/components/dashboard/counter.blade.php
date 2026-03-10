@component('components.card')
    @slot('header')
        <div class="d-flex justify-content-between align-items-center w-100">
            <span>Counter Shin - <span id="currentSeamerLabel">Seamer 1</span></span>
            <span id="totalProduksiBadge" class="badge fs-6" style="background-color: #fd7e14; color: white;">
                Total Hari Ini: <span id="totalProduksiCount">0</span> Pcs
            </span>
        </div>
    @endslot

    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center justify-content-between">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button class="btn btn-sm btn-outline-primary" onclick="resetToToday()">
                <i class="bi bi-calendar-check"></i> Hari Ini
            </button>

            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-light text-muted">Riwayat:</span>
                <input type="date" id="filterDate" class="form-control" value="{{ date('Y-m-d') }}"
                    onchange="fetchRpmChartData('day')">
            </div>
        </div>

        <div class="input-group input-group-sm" style="width: auto;">
            <span class="input-group-text bg-primary text-white">Mesin:</span>
            <select id="filterSeamer" class="form-select" onchange="fetchRpmChartData('day')">
                <option value="Seamer1" selected>Seamer 1</option>
                <option value="Seamer2">Seamer 2</option>
                <option value="Seamer3">Seamer 3</option>
            </select>
        </div>
    </div>

    <div style="position: relative; height: 300px;">
        <canvas id="rpmChart"></canvas>
    </div>
@endcomponent
