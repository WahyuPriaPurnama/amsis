/**
 * Dashboard Counter Manager
 * Menangani visualisasi data produksi 3 Seamer secara real-time.
 */
const DashboardCounter = (() => {
    // State internal
    let chartInstance = null;
    const colors = {
        'Seamer1': '#fd7e14', // Orange (AMS)
        'Seamer2': '#0d6efd', // Blue
        'Seamer3': '#198754'  // Green
    };

    /**
     * Inisialisasi Chart.js dengan konfigurasi dasar
     */
    const initChart = () => {
        const ctx = document.getElementById('rpmChart')?.getContext('2d');
        if (!ctx) return;

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Produksi (pcs/jam)',
                    data: [],
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 500 }, // Sedikit animasi agar transisi smooth
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Unit (Pcs)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    };

    /**
     * Mengambil data dari API Laravel
     */
    const fetchData = async (range = 'day') => {
        const elDate = document.getElementById('filterDate');
        const elSeamer = document.getElementById('filterSeamer');
        const elLabel = document.getElementById('currentSeamerLabel');

        const date = elDate?.value || new Date().toLocaleDateString('en-CA');
        const seamerId = elSeamer?.value || 'Seamer1';

        try {
            // Efek visual saat loading
            document.getElementById('rpmChart').style.opacity = '0.6';

            const response = await fetch(`/api/counter/${range}?date=${date}&seamer_id=${seamerId}`);
            if (!response.ok) throw new Error('Network response was not ok');

            const json = await response.json();

            // Update Label Header jika ada
            if (elLabel) elLabel.innerText = elSeamer?.options[elSeamer.selectedIndex]?.text || seamerId;

            // Sinkronisasi data ke Chart
            chartInstance.data.labels = json.labels;
            chartInstance.data.datasets[0].data = json.counter;
            chartInstance.data.datasets[0].label = `Produksi ${seamerId}`;

            // Update warna dinamis
            const themeColor = colors[seamerId] || colors['Seamer1'];
            chartInstance.data.datasets[0].borderColor = themeColor;
            chartInstance.data.datasets[0].backgroundColor = `${themeColor}1A`; // 1A = 10% opacity hex

            chartInstance.update();
        } catch (error) {
            console.error("Dashboard Error:", error);
        } finally {
            document.getElementById('rpmChart').style.opacity = '1';
        }
    };

    /**
     * Logika Auto Refresh
     */
    const startAutoRefresh = (ms = 60000) => {
        setInterval(() => {
            const elDate = document.getElementById('filterDate');
            const today = new Date().toLocaleDateString('en-CA');

            // Hanya refresh jika user sedang melihat data hari ini
            if (elDate && elDate.value === today) {
                fetchData();
            }
        }, ms);
    };

    // Public API
    return {
        init: () => {
            initChart();
            fetchData();
            startAutoRefresh();
        },
        refresh: fetchData,
        reset: () => {
            const elDate = document.getElementById('filterDate');
            if (elDate) {
                elDate.value = new Date().toLocaleDateString('en-CA');
                fetchData();
            }
        }
    };
})();

// Jalankan saat DOM siap
document.addEventListener("DOMContentLoaded", DashboardCounter.init);

// Bridge untuk attribute onclick di HTML
window.fetchRpmChartData = DashboardCounter.refresh;
window.resetToToday = DashboardCounter.reset;