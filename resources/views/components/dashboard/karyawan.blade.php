 @component('components.card')
     @slot('header')
         Karyawan
     @endslot

     <div id="employee-chart" class="mt-3">
         <canvas id="chartCanvas"></canvas>
     </div>
 @endcomponent
