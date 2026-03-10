document.addEventListener("DOMContentLoaded", () => {
    const rpmChartCtx = document.getElementById('rpmChart').getContext('2d');
    const rpmChart = new Chart(rpmChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Produksi (pcs/jam)',
                data: [],
                borderColor: '#fd7e14', // Orange khas AMS
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Unit' }
                }
            }
        }
    });

    // Reset ke hari ini
    window.resetToToday = function () {
        const dateInput = document.getElementById('filterDate');
        const today = new Date().toLocaleDateString('en-CA');

        if (dateInput) {
            dateInput.value = today;
            fetchRpmChartData('day');
        }
    }

    // Ambil data dengan parameter Seamer ID
    window.fetchRpmChartData = async function (range = 'day') {
        try {
            const dateInput = document.getElementById('filterDate');
            const seamerInput = document.getElementById('filterSeamer'); // Dropdown Seamer

            const selectedDate = dateInput ? dateInput.value : '';
            const selectedSeamer = seamerInput ? seamerInput.value : 'Seamer1';

            // Mengirim request dengan query string ?date=...&seamer_id=...
            const response = await fetch(`/api/counter/${range}?date=${selectedDate}&seamer_id=${selectedSeamer}`);
            const json = await response.json();

            // Update Label & Data secara dinamis
            rpmChart.data.labels = json.labels;
            rpmChart.data.datasets[0].label = `Produksi ${selectedSeamer}`;
            rpmChart.data.datasets[0].data = json.counter;

            // Ubah warna berdasarkan seamer agar user tidak bingung
            const colors = { 'Seamer1': '#fd7e14', 'Seamer2': '#0d6efd', 'Seamer3': '#198754' };
            rpmChart.data.datasets[0].borderColor = colors[selectedSeamer] || '#fd7e14';

            rpmChart.update();
        } catch (error) {
            console.error("Gagal sinkronisasi Chart:", error);
        }
    }

    // Auto-refresh: Dipercepat ke 1 menit (60000ms) untuk data produksi real-time
    function scheduleAutoRefresh() {
        setInterval(() => {
            const dateInput = document.getElementById('filterDate');
            const today = new Date().toLocaleDateString('en-CA');

            if (dateInput && dateInput.value === today) {
                console.log("Auto-refresh data...");
                fetchRpmChartData();
            }
        }, 60000);
    }

    // Init
    fetchRpmChartData();
    scheduleAutoRefresh();
});