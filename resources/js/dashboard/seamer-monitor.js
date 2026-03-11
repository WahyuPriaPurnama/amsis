/**
 * Dashboard Monitor Unified System
 * Mengelola Gauge, Grafik Produksi, dan Total Badge
 */
const DashboardMonitor = (() => {
    let gaugeChart = null;
    let historyChart = null;
    let lastDataTime = Date.now();

    const colors = {
        'Seamer1': '#fd7e14', // Orange
        'Seamer2': '#0d6efd', // Blue
        'Seamer3': '#198754'  // Green
    };

    const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num || 0);

    // 1. Inisialisasi Semua Chart
    const initCharts = () => {
        // --- GAUGE CHART ---
        const gaugeCtx = document.getElementById('rpmGauge')?.getContext('2d');
        if (gaugeCtx) {
            gaugeChart = new Chart(gaugeCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [0, 120],
                        backgroundColor: [colors.Seamer1, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        cutout: '80%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                },
                plugins: [{
                    id: 'centerText',
                    beforeDraw(chart) {
                        const { ctx, width, height } = chart;
                        const rpm = chart.data.datasets[0].data[0];
                        const counter = chart.config._counterValue || 0;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = 'bold 22px sans-serif';
                        ctx.fillStyle = chart.data.datasets[0].backgroundColor[0];
                        ctx.fillText(`${rpm} CPM`, width / 2, height / 2 - 5);
                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#6c757d';
                        ctx.fillText(`Total: ${formatNumber(counter)}`, width / 2, height / 2 + 25);

                        // Cek Delay (Indikator Offline)
                        if (Date.now() - lastDataTime > 8000) {
                            ctx.fillStyle = '#dc3545';
                            ctx.font = 'italic 11px sans-serif';
                            ctx.fillText("Menunggu Data...", width / 2, height / 2 + 45);
                        }
                        ctx.restore();
                    }
                }]
            });
        }

        // --- HISTORY CHART ---
        const historyCtx = document.getElementById('rpmChart')?.getContext('2d');
        if (historyCtx) {
            historyChart = new Chart(historyCtx, {
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
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Pcs' } },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.dataset.label}: ${formatNumber(c.parsed.y)} Pcs`
                            }
                        }
                    }
                }
            });
        }
    };

    // 2. Fungsi Ambil Data Tunggal
    const refreshData = async () => {
        const seamerId = document.getElementById('filterSeamer')?.value || 'Seamer1';
        const date = document.getElementById('filterDate')?.value || new Date().toLocaleDateString('en-CA');
        const themeColor = colors[seamerId] || colors.Seamer1;

        try {
            const response = await fetch(`/api/counter/day?date=${date}&seamer_id=${seamerId}`);
            const json = await response.json();

            // Update Gauge
            const latestRPM = json.rpm?.length > 0 ? json.rpm[json.rpm.length - 1] : 0;
            const latestCounter = json.counter?.length > 0 ? json.counter[json.counter.length - 1] : 0;
            if (gaugeChart) {
                gaugeChart.data.datasets[0].data = [latestRPM, Math.max(0, 120 - latestRPM)];
                gaugeChart.data.datasets[0].backgroundColor[0] = themeColor;
                gaugeChart.config._counterValue = latestCounter;
                gaugeChart.update('none'); // Update tanpa animasi berat
            }

            // Update Line Chart
            if (historyChart) {
                historyChart.data.labels = json.labels;
                historyChart.data.datasets[0].data = json.counter;
                historyChart.data.datasets[0].borderColor = themeColor;
                historyChart.data.datasets[0].backgroundColor = `${themeColor}1A`;
                historyChart.update();
            }

            // Update UI Elements
            const elTotal = document.getElementById('totalProduksiCount');
            const elBadge = document.getElementById('totalProduksiBadge');
            const elLabel = document.getElementById('currentSeamerLabel');

            if (elTotal) elTotal.innerText = formatNumber(json.total_produksi);
            if (elBadge) elBadge.style.backgroundColor = themeColor;
            if (elLabel) elLabel.innerText = document.querySelector(`#filterSeamer option[value="${seamerId}"]`)?.text;

            if (json.last_timestamp) lastDataTime = json.last_timestamp * 1000;

        } catch (error) {
            console.error("Dashboard Sync Error:", error);
        }
    };

    return {
        init: () => {
            initCharts();
            refreshData();
            // Refresh data tiap 3 detik
            setInterval(refreshData, 3000);
            // Redraw gauge tiap detik (untuk update indikator OFFLINE)
            setInterval(() => gaugeChart && gaugeChart.draw(), 1000);
        },
        reset: () => {
            document.getElementById('filterDate').value = new Date().toLocaleDateString('en-CA');
            refreshData();
        }
    };
})();

// Jalankan saat halaman siap
document.addEventListener("DOMContentLoaded", DashboardMonitor.init);

// Bridge untuk HTML event
window.fetchRpmChartData = DashboardMonitor.refresh;
window.resetToToday = DashboardMonitor.reset;