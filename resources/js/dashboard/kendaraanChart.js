document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById('vehicleCanvas');
    if (!canvas) return;

    const vehicleCtx = canvas.getContext('2d');
    new Chart(vehicleCtx, {
        type: 'bar',
        data: {
            // Menggunakan label yang sama dengan karyawan agar urutan plant sinkron
            labels: window.employeeLabels, 
            datasets: [{
                label: 'Kendaraan',
                data: window.vehicleData,
                backgroundColor: [
                    '#6c757d', // AMS
                    '#6610f2', // ELN1
                    '#fd7e14', // ELN2
                    '#198754', // BOFI
                    '#0dcaf0', // HK
                    '#d63384', // RMM
                    '#20c997', // Tambahan jika ada plant ke-7
                    '#ffc107'  // Tambahan jika ada plant ke-8
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
});