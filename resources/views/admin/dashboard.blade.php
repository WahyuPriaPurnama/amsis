@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @if (session('feature_changes'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5>🔔 Info Peningkatan Fitur:</h5>
                <ul>
                    @foreach (session('feature_changes') as $note)
                        <li>
                            {{ $note }}
                            @if ($loop->remaining < 3)
                                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">BARU</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-md-6">
                @component('components.dashboard.karyawan')
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.dashboard.kendaraan')
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
        // Mengambil data dinamis dari Controller
        const serverData = @json($chartData);

        // Data karyawan dan kendaraan kini mengikuti jumlah plant di database
        window.employeeLabels = serverData.labels;
        window.employeeData = serverData.employee;
        window.vehicleData = serverData.vehicle;
    </script>
@endsection
