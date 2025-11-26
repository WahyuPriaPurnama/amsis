document.addEventListener("DOMContentLoaded", () => {
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const { ctx, width, height } = chart;
            const rpm = chart.data.datasets[0].data[0];
            const counter = chart.config._counterValue || 0;

            ctx.save();
            ctx.font = 'bold 20px sans-serif';
            ctx.fillStyle = '#198754';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(`${rpm} CPM`, width / 2, height / 2 - 10);

            ctx.font = '16px sans-serif';
            ctx.fillStyle = '#0d6efd';
            ctx.fillText(`Counter: ${counter}`, width / 2, height / 2 + 15);
            ctx.restore();
        }
    };

    const rpmScaleLabelsPlugin = {
        id: 'rpmScaleLabels',
        beforeDraw(chart) {
            const { ctx, chartArea, width, height } = chart;
            const centerX = width / 2;
            const centerY = height / 2;
            const radius = Math.min(width, chartArea.bottom - chartArea.top) / 2.1;

            ctx.save();
            ctx.font = '12px sans-serif';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const maxRPM = 120;
            const step = 20;

            for (let rpm = 0; rpm <= maxRPM; rpm += step) {
                const percent = rpm / maxRPM;
                const angle = Math.PI * (percent - 1);
                const x = centerX + radius * Math.cos(angle);
                const y = centerY + radius * Math.sin(angle) + 50;
                ctx.fillText(rpm.toString(), x, y);
            }
            ctx.restore();
        }
    };

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
        } catch (error) {
            console.error("Gagal memuat data CPM Gauge:", error);
        }
    }

    // init
    fetchRpmGaugeData();
    setInterval(fetchRpmGaugeData, 2000);
});