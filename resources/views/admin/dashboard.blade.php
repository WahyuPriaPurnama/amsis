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
                        SUHU & KELEMBAPAN
                    @endslot
                    <div class="mt-3">
                        <canvas id="sensorChart"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.card')
                    @slot('header')
                        CPM & Counter | Shin i
                    @endslot
                    <div class="mt-3">
                        <canvas id="rpmChart"></canvas>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.card')
                    @slot('header')
                        CPM & Counter | Shin i
                    @endslot
                    <div class="mt-3 chart-wrapper text-center">
                        <canvas id="rpmGauge" width="200" height="150"></canvas>
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
            setInterval(fetchSensorData, 10000);
        });

        document.addEventListener("DOMContentLoaded", () => {
            // Inisialisasi rpmChart
            const rpmChartCtx = document.getElementById('rpmChart').getContext('2d');
            const rpmChart = new Chart(rpmChartCtx, {
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

            // Plugin: Teks RPM & Counter di tengah
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw(chart) {
                    const {
                        ctx,
                        width,
                        height
                    } = chart;
                    const rpm = chart.data.datasets[0].data[0];
                    const counter = chart.config._counterValue || 0;

                    ctx.save();
                    ctx.font = 'bold 20px sans-serif';
                    ctx.fillStyle = '#198754';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(`${rpm} RPM`, width / 2, height / 2 - 10);

                    ctx.font = '16px sans-serif';
                    ctx.fillStyle = '#0d6efd';
                    ctx.fillText(`Counter: ${counter}`, width / 2, height / 2 + 15);
                    ctx.restore();
                }
            };

            // Plugin: Angka skala RPM melingkar
            const rpmScaleLabelsPlugin = {
                id: 'rpmScaleLabels',
                beforeDraw(chart) {
                    const {
                        ctx,
                        chartArea,
                        width,
                        height
                    } = chart;
                    const centerX = width / 2;
                    const centerY = height / 2;
                    const radius = Math.min(width, chartArea.bottom - chartArea.top) / 2.1;

                    ctx.save();
                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#6c757d';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    const maxRPM = 120;
                    const step = 20;

                    for (let rpm = 0; rpm <= maxRPM; rpm += step) {
                        const percent = rpm / maxRPM;
                        const angle = Math.PI * (percent - 1); // -90° ke +90°
                        const x = centerX + radius * Math.cos(angle);
                        const y = centerY + radius * Math.sin(angle) + 50;
                        ctx.fillText(rpm.toString(), x, y);
                    }

                    ctx.restore();
                }
            };

            // Inisialisasi rpmGauge
            const rpmGaugeCtx = document.getElementById('rpmGauge').getContext('2d');
            const rpmGauge = new Chart(rpmGaugeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['RPM'],
                    datasets: [{
                        data: [0, 120],
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.9)',
                            'rgba(233, 236, 239, 0.5)'
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
                        }
                    }
                },
                plugins: [centerTextPlugin, rpmScaleLabelsPlugin]
            });

            // ✅ Fungsi fetch tunggal untuk kedua chart
            async function fetchRpmData() {
                try {
                    const response = await fetch('/api/counter');
                    const json = await response.json();

                    // Update rpmChart
                    rpmChart.data.labels = json.labels;
                    rpmChart.data.datasets[0].data = json.rpm;
                    rpmChart.data.datasets[1].data = json.counter;
                    rpmChart.update();

                    // Update rpmGauge
                    const latestRPM = json.rpm.at(-1) || 0;
                    const latestCounter = json.counter.at(-1) || 0;
                    rpmGauge.data.datasets[0].data = [latestRPM, 120 - latestRPM];
                    rpmGauge.config._counterValue = latestCounter;
                    rpmGauge.update();
                } catch (error) {
                    console.error("Gagal memuat data RPM:", error);
                }
            }

            fetchRpmData();
            setInterval(fetchRpmData, 3000); 
        });
    </script>
@endsection
