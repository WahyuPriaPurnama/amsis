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

        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between">
                    JUMLAH KARYAWAN

                    <div>
                        @php
                            $hour = now()->hour;
                            $greeting = match (true) {
                                $hour < 11 => 'Selamat Pagi',
                                $hour < 15 => 'Selamat Siang',
                                $hour < 18 => 'Selamat Sore',
                                default => 'Selamat Malam',
                            };
                        @endphp
                        {{ $greeting }}
                    </div>
                </div>
            @endslot

            <div id="employee-chart" class="mt-3">
                <canvas id="chartCanvas"></canvas>
            </div>
        @endcomponent
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
@endsection
