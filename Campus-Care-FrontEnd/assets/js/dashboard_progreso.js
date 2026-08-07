const METRICAS_INFO = {
    ESTRES: { label: 'Estrés', icon: '😣', color: 'danger' },
    ANSIEDAD: { label: 'Ansiedad', icon: '😟', color: 'warning' },
    SUENO: { label: 'Sueño', icon: '😴', color: 'info' },
    ANIMO: { label: 'Ánimo', icon: '😊', color: 'success' },
    RELACIONES: { label: 'Relaciones', icon: '💕', color: 'secondary' },
    MOTIVACION: { label: 'Motivación', icon: '💪', color: 'primary' },
};

document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('estudiante');
    if (!usuario) return;

    try {
        const dashboard = await window.CampusCareApi.request(`/api/dashboard/estudiante/${usuario.id}`);
        renderDashboard(dashboard);
    } catch (error) {
        console.error('Error cargando dashboard:', error);
        document.getElementById('estadoVacio').classList.remove('d-none');
    }

    function renderDashboard(data) {
        const hayDatos = data.series && data.series.some(s => s.valores.some(v => v !== null));

        if (!hayDatos) {
            document.getElementById('estadoVacio').classList.remove('d-none');
            return;
        }

        document.getElementById('contenidoDashboard').classList.remove('d-none');

        // Gamificación: solo se muestra si el backend la incluye
        if (data.puntosTotales !== undefined && data.rachaActual !== undefined) {
            document.getElementById('puntosTotales').textContent = data.puntosTotales;
            document.getElementById('rachaActual').textContent = data.rachaActual;
            document.getElementById('gamificacionContainer').classList.remove('d-none');
        }

        renderGrafico(data.fechas, data.series);
        renderTarjetasResumen(data.series);

        //if (data.mensajeExito) {
        //    mostrarModalFelicitacion(data.mensajeExito);
        //}
    }

    function renderGrafico(fechas, series) {
        const ctx = document.getElementById('progressChart').getContext('2d');
        const datasets = series.map(serie => ({
            label: METRICAS_INFO[serie.metrica]?.label || serie.metrica,
            data: serie.valores,
            spanGaps: false, // deja el hueco visible en días sin evaluación
        }));

        new Chart(ctx, {
            type: 'line',
            data: { labels: fechas, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5, // corregido: la escala real es 1-5, no 1-10
                        ticks: { stepSize: 1 },
                        title: { display: true, text: 'Puntuación (1-5)' }
                    },
                    x: { title: { display: true, text: 'Fechas de evaluación' } }
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });
    }

    function renderTarjetasResumen(series) {
        const container = document.getElementById('tarjetasResumen');

        container.innerHTML = series.map(serie => {
            // Último valor no-nulo de la serie (la evaluación más reciente disponible)
            const valoresValidos = serie.valores.filter(v => v !== null);
            const score = valoresValidos.length > 0 ? valoresValidos[valoresValidos.length - 1] : null;
            const info = METRICAS_INFO[serie.metrica] || { label: serie.metrica, icon: '❔', color: 'secondary' };

            if (score === null) {
                return `
                    <div class="col-md-4 col-lg-2 mb-3">
                        <div class="card text-center h-100 border-${info.color}">
                            <div class="card-body p-3">
                                <div class="display-6 mb-2">${info.icon}</div>
                                <h6 class="card-title mb-1">${info.label}</h6>
                                <div class="h4 text-muted mb-1">-</div>
                                <small class="text-muted">Sin datos</small>
                            </div>
                        </div>
                    </div>
                `;
            }

            const nivel = score <= 2 ? 'Bajo' : (score <= 3 ? 'Moderado' : 'Alto');

            return `
                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="card text-center h-100 border-${info.color}">
                        <div class="card-body p-3">
                            <div class="display-6 mb-2">${info.icon}</div>
                            <h6 class="card-title mb-1">${info.label}</h6>
                            <div class="h4 text-${info.color} mb-1">${score}/5</div>
                            <small class="text-muted">${nivel}</small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }


});