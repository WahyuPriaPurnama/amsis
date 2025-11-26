@component('components.card')
    @slot('header')
        Kendaraan
    @endslot
    <div id="vehicle-chart" class="mt-3">
        <canvas id="vehicleCanvas"></canvas>
    </div>
@endcomponent
