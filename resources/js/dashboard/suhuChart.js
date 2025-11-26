document.addEventListener("DOMContentLoaded", () => {
    function scheduleHourlyFetch() {
        const now = new Date();
        const nextHour = new Date(now);
        nextHour.setMinutes(0, 0, 0); // reset ke jam bulat
        nextHour.setHours(now.getHours() + 1); // jam berikutnya

        const delay = nextHour - now; // selisih waktu dalam ms

        setTimeout(() => {
            fetchSensorData(); // panggil pertama kali di jam bulat
            setInterval(fetchSensorData, 3600000); // lalu setiap 1 jam
        }, delay);
    }
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
            const response = await fetch('/api/suhu');
            const json = await response.json();

            sensorChart.data.labels = json.labels;
            sensorChart.data.datasets[0].data = json.temperature;
            sensorChart.data.datasets[1].data = json.humidity;
            sensorChart.update();
        } catch (error) {
            console.error("Gagal memuat data suhu:", error);
        }
    }

    fetchSensorData();
    scheduleHourlyFetch();
});