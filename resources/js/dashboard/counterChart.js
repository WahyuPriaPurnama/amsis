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

    window.fetchRpmChartData = async function (range = 'day') {
        try {
            const response = await fetch(`/api/counter/${range}`);
            const json = await response.json();
            rpmChart.data.labels = json.labels;
            rpmChart.data.datasets[0].data = json.counter;
            rpmChart.update();
        } catch (error) {
            console.error("Gagal memuat data CPM Chart:", error);
        }
    }

    function scheduleHourlyRpmChartFetch() {
        const now = new Date();
        const nextHour = new Date(now);
        nextHour.setMinutes(0, 0, 0);
        nextHour.setHours(now.getHours() + 1);
        const delay = nextHour - now;

        setTimeout(() => {
            fetchRpmChartData();
            setInterval(fetchRpmChartData, 3600000);
        }, delay);
    }

    // init
    fetchRpmChartData();
    scheduleHourlyRpmChartFetch();
});