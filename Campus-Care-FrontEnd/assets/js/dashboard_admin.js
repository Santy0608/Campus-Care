document.addEventListener('DOMContentLoaded', function () {
    const chartData = window.dashboardAdminData;

    if (!chartData) return;

    if (chartData.datasets && chartData.datasets.length > 0 && chartData.datasets[0].data.some(d => d > 0)) {
        const ctx = document.getElementById('radarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: chartData,
            options: {
                responsive: true,
                aspectRatio: 1,
                plugins: {
                    title: { display: false },
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: { display: true, color: 'rgba(0, 0, 0, 0.1)' },
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: { stepSize: 1, color: 'rgba(0, 0, 0, 0.6)' },
                        pointLabels: { font: { size: 14, weight: 'bold' } }
                    }
                }
            }
        });
    } else {
        const container = document.getElementById('radarChart').parentElement.parentElement;
        container.innerHTML = '<div class="alert alert-info text-center mt-3" role="alert"><i class="fas fa-info-circle me-2"></i> Aún no hay suficientes evaluaciones para mostrar el gráfico poblacional.</div>';
    }
});









