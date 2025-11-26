@component('components.card')
    @slot('header')
        Counter Shin I 10
    @endslot
    <div class="btn-group mb-2">
        <button class="btn btn-sm btn-primary" onclick="fetchRpmChartData('day')">Hari ini</button>
        <button class="btn btn-sm btn-secondary" onclick="fetchRpmChartData('week')">Seminggu</button>
    </div>
    <canvas id="rpmChart"></canvas>
@endcomponent
