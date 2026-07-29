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

    await cargarTotalEstudiantes();
    mostrarRadarNoDisponible();
});

async function cargarTotalEstudiantes() {
    const totalEl = document.getElementById('totalEstudiantes');

    try {
        const usuarios = await window.CampusCareApi.request('/api/usuarios/listado-usuarios');
        const totalEstudiantes = (Array.isArray(usuarios) ? usuarios : [])
            .filter((u) => !u.admin && !(u.roles || []).includes('ADMIN'))
            .length;

        totalEl.textContent = totalEstudiantes;
    } catch (error) {
        console.error('Error cargando el listado de usuarios:', error);
        totalEl.textContent = '-';
    }
}

// El backend aún no expone un endpoint agregado de autoevaluaciones para el
// rol ADMIN (ver documentación de endpoints: solo existe
// /api/dashboard/estudiante/{idUsuario}, restringido a ESTUDIANTE).
// Se muestra el estado vacío en lugar de inventar una llamada a una ruta
// inexistente.
function mostrarRadarNoDisponible() {
    document.getElementById('evaluacionesHoy').textContent = 'N/D';
    document.getElementById('riesgoPromedio').textContent = 'N/D';
    document.getElementById('riesgoCategoria').textContent = 'Categoría: N/A';

    const container = document.getElementById('radarChart').parentElement.parentElement;
    container.innerHTML = `
        <div class="alert alert-info text-center mt-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            El backend todavía no expone un endpoint de métricas agregadas para el panel administrativo.
        </div>`;
}
