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
                @component('components.card')
                    @slot('header')
                        JUMLAH KARYAWAN
                    @endslot

                    <div id="employee-chart" class="mt-3">
                        <canvas id="chartCanvas"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-4">
                @component('components.card')
                    @slot('header')
                        JUMLAH KENDARAAN
                    @endslot
                    <div id="vehicle-chart" class="mt-3">
                        <canvas id="vehicleCanvas"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-4">
                @component('components.card')
                    @slot('header')
                        SUHU & KELEMBAPAN (Realtime)
                    @endslot
                    <div class="mt-3">
                        <canvas id="sensorChart"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.card')
                    @slot('header')
                        RPM & COUNTER (Realtime)
                    @endslot
                    <div class="mt-3">
                        <canvas id="rpmChart"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.card')
                    @slot('header')
                        RPM & COUNTER (Speedometer)
                    @endslot
                    <div class="mt-3 text-center">
                        <canvas id="rpmGauge" width="250" height="250"></canvas>
                        <div class="mt-3 fs-5">
                            <strong>RPM:</strong> <span id="rpmValue" class="text-success fw-bold">0</span><br>
                            <strong>Counter:</strong> <span id="counterValue" class="text-primary fw-bold">0</span>
                        </div>
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
                        label: 'Karyawan',
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

        document.addEventListener("DOMContentLoaded", () => {
            const vehicleCtx = document.getElementById('vehicleCanvas').getContext('2d');
            new Chart(vehicleCtx, {
                type: 'bar',
                data: {
                    labels: ['AMS', 'ELN1', 'ELN2', 'BOFI', 'HK', 'RMM'],
                    datasets: [{
                        label: 'Kendaraan',
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
        document.addEventListener("DOMContentLoaded", () => {
            const sensorCtx = document.getElementById('sensorChart').getContext('2d');
            const sensorChart = new Chart(sensorCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Temperature (°C)',
                            data: [],
                            borderColor: 'red',
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Humidity (%)',
                            data: [],
                            borderColor: 'blue',
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    animation: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            async function fetchSensorData() {
                try {
                    const response = await fetch('/api/sensor');
                    const json = await response.json();

                    sensorChart.data.labels = json.labels;
                    sensorChart.data.datasets[0].data = json.temperature;
                    sensorChart.data.datasets[1].data = json.humidity;
                    sensorChart.update();
                } catch (error) {
                    console.error("Gagal memuat data sensor:", error);
                }
            }

            fetchSensorData();
            setInterval(fetchSensorData, 5000); // update setiap 5 detik
        });

        document.addEventListener("DOMContentLoaded", () => {
            const rpmCtx = document.getElementById('rpmChart').getContext('2d');
            const rpmChart = new Chart(rpmCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'RPM',
                            data: [],
                            borderColor: 'green',
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Counter',
                            data: [],
                            borderColor: 'orange',
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    animation: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            async function fetchRpmData() {
                try {
                    const response = await fetch('/api/counter');
                    const json = await response.json();

                    rpmChart.data.labels = json.labels;
                    rpmChart.data.datasets[0].data = json.rpm;
                    rpmChart.data.datasets[1].data = json.counter;
                    rpmChart.update();
                } catch (error) {
                    console.error("Gagal memuat data RPM:", error);
                }
            }

            fetchRpmData();
            setInterval(fetchRpmData, 5000); // update setiap 5 detik
        });

        document.addEventListener("DOMContentLoaded", () => {
            const rpmCtx = document.getElementById('rpmGauge').getContext('2d');
            const rpmGauge = new Chart(rpmCtx, {
                type: 'doughnut',
                data: {
                    labels: ['RPM'],
                    datasets: [{
                        data: [0, 120],
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.9)', // RPM aktif (hijau)
                            'rgba(233, 236, 239, 0.5)' // RPM sisa (abu)
                        ],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            enabled: false
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'RPM Speedometer',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });

            async function fetchRpmData() {
                try {
                    const response = await fetch('/api/counter');
                    const json = await response.json();

                    const latestRPM = json.rpm.at(-1) || 0;
                    const latestCounter = json.counter.at(-1) || 0;

                    rpmGauge.data.datasets[0].data = [latestRPM, 120 - latestRPM];
                    rpmGauge.update();

                    document.getElementById('rpmValue').textContent = latestRPM;
                    document.getElementById('counterValue').textContent = latestCounter;
                } catch (error) {
                    console.error("Gagal memuat data RPM:", error);
                }
            }

            fetchRpmData();
            setInterval(fetchRpmData, 5000);
        });
    </script>
@endsection
