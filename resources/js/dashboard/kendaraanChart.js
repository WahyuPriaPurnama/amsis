let vehicleChartInstance = null;

function renderVehicleChart(labels, data) {
    const canvas = document.getElementById('vehicleCanvas');
    if (!canvas) return; // Guard clause jika elemen tidak ada di DOM

    // 1. Ambil data dari parameter, jika kosong ambil fallback dari window object
    const chartLabels = labels || window.dashboardChartData?.labels || window.employeeLabels || [];
    const chartData = data || window.dashboardChartData?.vehicleData || window.vehicleData || [];

    // 2. Destroy instance chart lama jika sudah ada (mencegah error "Canvas is already in use")
    if (vehicleChartInstance) {
        vehicleChartInstance.destroy();
    }

    // 3. Buat chart kendaraan baru
    const vehicleCtx = canvas.getContext('2d');
    vehicleChartInstance = new Chart(vehicleCtx, {
        type: 'bar',
        data: {
            labels: chartLabels, 
            datasets: [{
                label: 'Kendaraan',
                data: chartData,
                backgroundColor: [
                    '#6c757d', // AMS
                    '#6610f2', // ELN1
                    '#fd7e14', // ELN2
                    '#198754', // BOFI
                    '#0dcaf0', // HK
                    '#d63384', // RMM
                    '#20c997', // Plant 7
                    '#ffc107'  // Plant 8
                ],
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Pemicu Fallback (Jika file JS dipanggil secara independen)
document.addEventListener('DOMContentLoaded', () => renderVehicleChart());
document.addEventListener('livewire:navigated', () => renderVehicleChart());