@component('components.card')
    @slot('header')
        Suhu
    @endslot
    <div class="mt-3">
        <canvas id="sensorChart"></canvas>
    </div>
@endcomponent
