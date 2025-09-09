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
        <div class="row">
            <div class="col">
                @component('components.card')
                    @slot('header')
                            JUMLAH KARYAWAN
                    @endslot

                    <div id="employee-chart" class="mt-3">
                        <canvas id="chartCanvas"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col">
                @component('components.card')
                    @slot('header')
                        JUMLAH KENDARAAN
                    @endslot

                    <div id="vehicle-chart" class="mt-3">
                        <canvas id="vehicleCanvas"></canvas>
                    </div>
                @endcomponent
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.getElementById('chartCanvas').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['AMS', 'ELN1', 'ELN2', 'BOFI', 'HK', 'RMM'],
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: [{{ $ams }}, {{ $eln1 }}, {{ $eln2 }},
                            {{ $bofi }}, {{ $hk }}, {{ $rmm }}
                        ],
                        backgroundColor: [
                            '#198754', // AMS - hijau
                            '#0d6efd', // ELN1 - biru
                            '#ffc107', // ELN2 - kuning
                            '#dc3545', // BOFI - merah
                            '#6f42c1', // HK - ungu
                            '#20c997' // RMM - teal
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const vehicleCtx = document.getElementById('vehicleCanvas').getContext('2d');
            new Chart(vehicleCtx, {
                type: 'bar',
                data: {
                    labels: ['AMS', 'ELN1', 'ELN2', 'BOFI', 'HK', 'RMM'],
                    datasets: [{
                        label: 'Jumlah Kendaraan',
                        data: [{{ $ams_vehicles }}, {{ $eln1_vehicles }}, {{ $eln2_vehicles }},
                            {{ $bofi_vehicles }}, {{ $hk_vehicles }}, {{ $rmm_vehicles }}
                        ],
                        backgroundColor: [
                            '#6c757d', // AMS - abu
                            '#6610f2', // ELN1 - indigo
                            '#fd7e14', // ELN2 - oranye
                            '#198754', // BOFI - hijau
                            '#0dcaf0', // HK - cyan
                            '#d63384' // RMM - pink
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
