@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @if (session('feature_changes'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5>🔔 Info Peningkatan Fitur:</h5>
                <ul>
                    @foreach (session('feature_changes') as $date => $note)
                        <li><strong>{{ $date }}:</strong> {{ $note }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row g-4">
            <div class="col-md-4">
                @component('components.dashboard.karyawan')
                @endcomponent
            </div>
            <div class="col-md-4">
                @component('components.dashboard.kendaraan')
                @endcomponent
            </div>
            <div class="col-md-4">
                @component('components.dashboard.suhu')
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.dashboard.counter')
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.dashboard.speed')
                @endcomponent
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.employeeData = [
            {{ $ams }}, {{ $eln1 }}, {{ $eln2 }},
            {{ $bofi }}, {{ $hk }}, {{ $rmm }}
        ];

        window.vehicleData = [{{ $ams_vehicles }}, {{ $eln1_vehicles }}, {{ $eln2_vehicles }},
            {{ $bofi_vehicles }}, {{ $hk_vehicles }}, {{ $rmm_vehicles }}
        ];
    </script>
@endsection
