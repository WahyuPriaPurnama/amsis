document.addEventListener("DOMContentLoaded", () => {
    const rpmChartCtx = document.getElementById('rpmChart').getContext('2d');
    const rpmChart = new Chart(rpmChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Counter',
                data: [],
                borderColor: 'orange',
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            animation: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    window.resetToToday = function () {
        const dateInput = document.getElementById('filterDate');
        const today = new Date().toLocaleDateString('en-CA'); // Format YYYY-MM-DD lokal

        if (dateInput) {
            dateInput.value = today;
            fetchRpmChartData('day');
        }
    }

    window.fetchRpmChartData = async function (range = 'day') {
        try {
            const dateInput = document.getElementById('filterDate');
            const selectedDate = dateInput ? dateInput.value : '';

            const response = await fetch(`/api/counter/day?date=${selectedDate}`);
            const json = await response.json();

            rpmChart.data.labels = json.labels;
            rpmChart.data.datasets[0].data = json.counter;
            rpmChart.update();
        } catch (error) {
            console.error("Gagal memuat data Chart:", error);
        }
    }

    function scheduleHourlyRpmChartFetch() {
        // Interval setiap 1 jam (3600000 ms)
        setInterval(() => {
            const dateInput = document.getElementById('filterDate');
            const today = new Date().toLocaleDateString('en-CA');

            // HANYA refresh jika input tanggal adalah hari ini
            if (dateInput && dateInput.value === today) {
                console.log("Auto-refresh data hari ini...");
                fetchRpmChartData();
            }
        }, 3600000);
    }

    // Inisialisasi awal
    fetchRpmChartData();
    scheduleHourlyRpmChartFetch();
});