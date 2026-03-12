/**
 * Dashboard Monitor Unified System
 * Indikator Offline Aktif: 3 Detik
 */
const DashboardMonitor = (() => {
    let gaugeChart = null;
    let historyChart = null;
    let lastDataTime = Date.now();
    const OFFLINE_THRESHOLD = 3000; // 3 detik dalam milidetik

    const colors = {
        'Seamer1': '#fd7e14', 
        'Seamer2': '#0d6efd', 
        'Seamer3': '#198754',
        'danger': '#dc3545'
    };

    const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num || 0);

    const initCharts = () => {
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

                        // Teks RPM
                        ctx.font = 'bold 22px sans-serif';
                        ctx.fillStyle = chart.data.datasets[0].backgroundColor[0];
                        ctx.fillText(`${rpm} CPM`, width / 2, height / 2 - 5);

                        // Teks Counter
                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#6c757d';
                        ctx.fillText(`Total Perjam: ${formatNumber(counter)}`, width / 2, height / 2 + 25);

                        // --- LOGIKA INDIKATOR OFFLINE ---
                        // Memeriksa selisih waktu sekarang dengan data terakhir
                        const timeDiff = Date.now() - lastDataTime;
                        
                        if (timeDiff > OFFLINE_THRESHOLD) {
                            ctx.fillStyle = colors.danger;
                            ctx.font = 'bold italic 11px sans-serif';
                            // Menambahkan info berapa detik terlambat untuk transparansi
                            const seconds = Math.floor(timeDiff / 1000);
                            ctx.fillText(`Menunggu Data... (${seconds}s)`, width / 2, height / 2 + 45);
                        } else {
                            ctx.fillStyle = '#198754';
                            ctx.font = 'bold 10px sans-serif';
                            ctx.fillText("● LIVE", width / 2, height / 2 + 45);
                        }
                        ctx.restore();
                    }
                }]
            });
        }

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
                        y: { beginAtZero: true },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    };

    const refreshData = async () => {
        const seamerId = document.getElementById('filterSeamer')?.value || 'Seamer1';
        const date = document.getElementById('filterDate')?.value || new Date().toLocaleDateString('en-CA');
        const themeColor = colors[seamerId] || colors.Seamer1;

        try {
            const response = await fetch(`/api/counter/day?date=${date}&seamer_id=${seamerId}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const json = await response.json();

            // Update timestamp data terakhir dari server (jika tersedia) 
            // atau gunakan waktu saat fetch berhasil diterima
            if (json.last_timestamp) {
                lastDataTime = json.last_timestamp * 1000;
            } else {
                lastDataTime = Date.now();
            }

            // Update Gauge
            const latestRPM = json.rpm?.length > 0 ? json.rpm[json.rpm.length - 1] : 0;
            const latestCounter = json.counter?.length > 0 ? json.counter[json.counter.length - 1] : 0;
            
            if (gaugeChart) {
                gaugeChart.data.datasets[0].data = [latestRPM, Math.max(0, 120 - latestRPM)];
                gaugeChart.data.datasets[0].backgroundColor[0] = themeColor;
                gaugeChart.config._counterValue = latestCounter;
                gaugeChart.update('none'); 
            }

            // Update Line Chart
            if (historyChart) {
                historyChart.data.labels = json.labels;
                historyChart.data.datasets[0].data = json.counter;
                historyChart.data.datasets[0].borderColor = themeColor;
                historyChart.data.datasets[0].backgroundColor = `${themeColor}1A`;
                historyChart.update('none');
            }

            // UI Elements
            document.getElementById('totalProduksiCount').innerText = formatNumber(json.total_produksi);
            document.getElementById('totalProduksiBadge').style.backgroundColor = themeColor;

        } catch (error) {
            console.error("Dashboard Sync Error:", error);
            // Jika fetch gagal, lastDataTime tidak diupdate, memicu indikator "Menunggu Data"
        }
    };

    return {
        init: () => {
            initCharts();
            refreshData();
            
            // Interval fetch data: tiap 2 detik (agar selalu di bawah threshold 3 detik)
            setInterval(refreshData, 2000);
            
            // Interval redraw UI: tiap 500ms agar teks "Menunggu data (Xs)" terupdate real-time
            setInterval(() => {
                if (gaugeChart) {
                    gaugeChart.draw();
                }
            }, 500);
        },
        reset: () => {
            document.getElementById('filterDate').value = new Date().toLocaleDateString('en-CA');
            refreshData();
        }
    };
})();

document.addEventListener("DOMContentLoaded", DashboardMonitor.init);