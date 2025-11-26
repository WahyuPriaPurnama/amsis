document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('chartCanvas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['AMS', 'ELN1', 'ELN2', 'BOFI', 'HK', 'RMM'],
            datasets: [{
                label: 'Karyawan',
                data: window.employeeData,
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