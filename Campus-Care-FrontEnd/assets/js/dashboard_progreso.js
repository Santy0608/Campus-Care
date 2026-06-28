document.addEventListener('DOMContentLoaded', function () {
    // Mostrar modal de gamificación si hay mensaje
    const mensaje = window.dashboardProgresoData ? window.dashboardProgresoData.mensaje : null;
    if (mensaje && mensaje.length > 0) {
        const modalBody = document.getElementById('modal-felicitacion-body');
        if (modalBody) {
            modalBody.innerHTML = mensaje;
        }
        const felicitacionModal = new bootstrap.Modal(document.getElementById('modal-felicitacion'));
        felicitacionModal.show();
    }

    // Inicializar gráfico de progreso
    const data = window.dashboardProgresoData;
    if (data && data.hasEvaluaciones) {
        const ctx = document.getElementById('progressChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: { stepSize: 1 },
                        title: { display: true, text: 'Puntuación (1-10)' }
                    },
                    x: {
                        title: { display: true, text: 'Fechas de evaluación' }
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
});
