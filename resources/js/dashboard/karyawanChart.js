let karyawanChartInstance = null;

function renderKaryawanChart(labels, data) {
    const ctx = document.getElementById('chartCanvas');
    if (!ctx) return; // Guard clause jika elemen tidak ditemukan

    // 1. Ambil data dari parameter, jika kosong ambil fallback dari window object
    const chartLabels = labels || window.dashboardChartData?.labels || window.employeeLabels || [];
    const chartData = data || window.dashboardChartData?.employeeData || window.employeeData || [];

    // 2. Destroy instance chart lama jika sudah ada (mencegah error canvas SPA)
    if (karyawanChartInstance) {
        karyawanChartInstance.destroy();
    }

    // 3. Buat chart baru
    karyawanChartInstance = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Karyawan',
                data: chartData,
                backgroundColor: [
                    '#198754', // AMS
                    '#0d6efd', // ELN1
                    '#ffc107', // ELN2
                    '#dc3545', // BOFI
                    '#6f42c1', // HK
                    '#20c997', // RMM
                    '#fd7e14', // Orange (Plant 7)
                    '#6c757d'  // Abu-abu (Plant 8)
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

// Pemicu Fallback (Jika file JS dipanggil secara terpisah)
document.addEventListener('DOMContentLoaded', () => renderKaryawanChart());
document.addEventListener('livewire:navigated', () => renderKaryawanChart());