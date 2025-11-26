@component('components.card')
    @slot('header')
        Speed Shin I 10
    @endslot
    <div class="mt-3 chart-wrapper text-center">
        <canvas id="rpmGauge" width="200" height="150"></canvas>
    </div>
@endcomponent
