@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container">

    <livewire:release-notes />

    <div class="row g-4">
        {{-- Bagian Grafik Karyawan --}}
        <div class="col-md-6">
            @component('components.dashboard.karyawan')
            @endcomponent
        </div>

        {{-- Bagian Grafik Kendaraan --}}
        <div class="col-md-6">
            @component('components.dashboard.kendaraan')
            @endcomponent
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    (function() {
        // Ambil data server dari Blade
        const serverData = @json($chartData ?? []);
        const labels = serverData.labels || [];
        const employeeData = serverData.employee || [];
        const vehicleData = serverData.vehicle || [];

        // Simpan ke namespace global untuk fallback
        window.dashboardChartData = {
            labels,
            employeeData,
            vehicleData
        };

        // Fungsi pembantu render
        window.initDashboardCharts = function() {
            if (typeof renderKaryawanChart === 'function') {
                renderKaryawanChart(labels, employeeData);
            }
            if (typeof renderVehicleChart === 'function') {
                renderVehicleChart(labels, vehicleData);
            }
        };

        // Eksekusi langsung saat komponen disuntikkan oleh Livewire SPA
        window.initDashboardCharts();
    })();

    // Pemicu saat pertama kali Full Page Load
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initDashboardCharts === 'function') {
            window.initDashboardCharts();
        }
    });

    // Pemicu saat navigasi antar menu via Livewire 4 SPA (wire:navigate)
    document.addEventListener('livewire:navigated', function() {
        if (typeof window.initDashboardCharts === 'function') {
            window.initDashboardCharts();
        }
    });
</script>
@endsection