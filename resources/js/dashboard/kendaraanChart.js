document.addEventListener("DOMContentLoaded", () => {
    const vehicleCtx = document.getElementById('vehicleCanvas').getContext('2d');
    new Chart(vehicleCtx, {
        type: 'bar',
        data: {
            labels: ['AMS', 'ELN1', 'ELN2', 'BOFI', 'HK', 'RMM'],
            datasets: [{
                label: 'Kendaraan',
                data: window.vehicleData,
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