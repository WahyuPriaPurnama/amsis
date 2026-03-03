document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('chartCanvas');
    if (!ctx) return; // Guard clause jika elemen tidak ditemukan

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            // Mengambil label dinamis (AMS, ELN1, dsb) dari window object
            labels: window.employeeLabels,
            datasets: [{
                label: 'Karyawan',
                data: window.employeeData,
                backgroundColor: [
                    '#198754', // AMS
                    '#0d6efd', // ELN1
                    '#ffc107', // ELN2
                    '#dc3545', // BOFI
                    '#6f42c1', // HK
                    '#20c997', // RMM
                    '#fd7e14', // Tambahan: Orange (untuk plant ke-7)
                    '#6c757d'  // Tambahan: Abu-abu (untuk plant ke-8)
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