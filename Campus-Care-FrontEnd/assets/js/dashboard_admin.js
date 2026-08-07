const CATEGORIAS_BIENESTAR = [
    { key: 'avg_estres', label: 'Estrés', color: 'rgba(255, 99, 132, 1)' },
    { key: 'avg_ansiedad', label: 'Ansiedad', color: 'rgba(255, 159, 64, 1)' },
    { key: 'avg_sueno', label: 'Sueño', color: 'rgba(54, 162, 235, 1)' },
    { key: 'avg_estado_animo', label: 'Estado de Ánimo', color: 'rgba(75, 192, 192, 1)' },
    { key: 'avg_relaciones', label: 'Relaciones', color: 'rgba(153, 102, 255, 1)' },
    { key: 'avg_motivacion', label: 'Motivación', color: 'rgba(201, 203, 207, 1)' },
];

document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) return;

    await cargarDashboardAdmin();
});

async function cargarDashboardAdmin() {
    try {
        const data = await window.CampusCareApi.request('/api/dashboard-admin');

        document.getElementById('totalEstudiantes').textContent = data.totalEstudiantes;
        document.getElementById('evaluacionesHoy').textContent = data.evaluacionesHoy;
        document.getElementById('riesgoPromedio').textContent = data.riesgoPromedio;
        document.getElementById('riesgoCategoria').textContent = `Categoría: ${data.riesgoCategoria}`;

        renderRadar(data);
    } catch (error) {
        console.error('Error cargando el dashboard admin:', error);
        mostrarErrorDashboard();
    }
}

function renderRadar(data) {
    const valores = CATEGORIAS_BIENESTAR.map((cat) => data[cat.key] || 0);
    const hayDatos = valores.some((v) => v > 0);

    if (!hayDatos) {
        mostrarErrorDashboard('Aún no hay suficientes evaluaciones para mostrar el gráfico poblacional.');
        return;
    }

    const ctx = document.getElementById('radarChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: CATEGORIAS_BIENESTAR.map((cat) => cat.label),
            datasets: [{
                label: 'Promedio general',
                data: valores,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                pointBackgroundColor: CATEGORIAS_BIENESTAR.map((cat) => cat.color),
            }],
        },
        options: {
            responsive: true,
            aspectRatio: 1,
            plugins: { legend: { display: false } },
            scales: {
                r: {
                    angleLines: { display: true, color: 'rgba(0, 0, 0, 0.1)' },
                    suggestedMin: 0,
                    suggestedMax: 5,
                    ticks: { stepSize: 1, color: 'rgba(0, 0, 0, 0.6)' },
                    pointLabels: { font: { size: 14, weight: 'bold' } },
                },
            },
        },
    });
}

function mostrarErrorDashboard(mensaje) {
    const container = document.getElementById('radarChart').parentElement.parentElement;
    container.innerHTML = `
        <div class="alert alert-info text-center mt-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            ${window.CampusCareApi.escapeHtml(mensaje || 'No se pudo cargar el dashboard.')}
        </div>`;
}