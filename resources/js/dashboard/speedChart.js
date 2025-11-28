document.addEventListener("DOMContentLoaded", () => {
    let lastDataTime = Date.now(); // waktu terakhir data diterima

    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const { ctx, width, height } = chart;
            const rpm = chart.data.datasets[0].data[0];
            const counter = chart.config._counterValue || 0;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // tulisan utama tetap
            ctx.font = 'bold 20px sans-serif';
            ctx.fillStyle = '#198754';
            ctx.fillText(`${rpm} CPM`, width / 2, height / 2 - 10);

            ctx.font = '16px sans-serif';
            ctx.fillStyle = '#0d6efd';
            ctx.fillText(`Counter: ${counter}`, width / 2, height / 2 + 15);

            // cek delay > 5 detik
            const now = Date.now();
            if (now - lastDataTime > 5000) {
                ctx.font = '14px sans-serif';
                ctx.fillStyle = '#dc3545'; // merah
                ctx.fillText("Menunggu data...", width / 2, height / 2 + 40);

                // atau titik merah kecil
                ctx.beginPath();
                ctx.arc(width / 2, height / 2 + 60, 6, 0, 2 * Math.PI);
                ctx.fill();
            }

            ctx.restore();
        }
    };

    const rpmScaleLabelsPlugin = { /* tetap sama */ };

    const rpmGaugeCtx = document.getElementById('rpmGauge').getContext('2d');
    const rpmGauge = new Chart(rpmGaugeCtx, {
        type: 'doughnut',
        data: {
            labels: ['CPM'],
            datasets: [{
                data: [0, 120],
                backgroundColor: ['rgba(25, 135, 84, 0.9)', 'rgba(233, 236, 239, 0.5)'],
                borderWidth: 0,
                circumference: 180,
                rotation: 270,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            plugins: { tooltip: { enabled: false }, legend: { display: false } }
        },
        plugins: [centerTextPlugin, rpmScaleLabelsPlugin]
    });

    async function fetchRpmGaugeData() {
        try {
            const response = await fetch('/api/counter');
            const json = await response.json();
            const latestRPM = json.rpm.at(-1) || 0;
            const latestCounter = json.counter.at(-1) || 0;

            rpmGauge.data.datasets[0].data = [latestRPM, 120 - latestRPM];
            rpmGauge.config._counterValue = latestCounter;
            rpmGauge.update();

            // update waktu terakhir data diterima
            lastDataTime = Date.now();
        } catch (error) {
            console.error("Gagal memuat data CPM Gauge:", error);
        }
    }

    // init
    fetchRpmGaugeData();
    setInterval(fetchRpmGaugeData, 2000);
});