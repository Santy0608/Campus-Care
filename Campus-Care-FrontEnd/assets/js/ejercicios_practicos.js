document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('estudiante');
    if (!usuario) return;

    const listaEjercicios = document.getElementById('listaEjercicios');
    const estadoVacio = document.getElementById('estadoVacio');
    const estadoError = document.getElementById('estadoError');

    try {
        const ejercicios = await window.CampusCareApi.request('/api/ejercicios-practicos/listado-ejercicios-practicos');
        const activos = Array.isArray(ejercicios) ? ejercicios.filter((e) => e.activo) : [];

        if (activos.length === 0) {
            estadoVacio.classList.remove('d-none');
            return;
        }

        renderEjercicios(activos);
    } catch (error) {
        console.error('Error cargando ejercicios prácticos:', error);
        estadoError.textContent = 'No pudimos cargar los ejercicios. Intenta de nuevo en unos minutos.';
        estadoError.classList.remove('d-none');
    }

    function formatearDuracion(segundos) {
        if (!segundos) return '';
        const minutos = Math.floor(segundos / 60);
        const resto = segundos % 60;
        if (minutos === 0) return `${resto}s`;
        return resto === 0 ? `${minutos} min` : `${minutos} min ${resto}s`;
    }

    function renderEjercicios(ejercicios) {
        listaEjercicios.innerHTML = ejercicios.map((ejercicio, index) => {
            const instrucciones = Array.isArray(ejercicio.instrucciones) ? ejercicio.instrucciones : [];
            const collapseId = `ejercicio-${index}`;

            return `
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                            <span class="flex-grow-1">${window.CampusCareApi.escapeHtml(ejercicio.nombre || '')}</span>
                            ${ejercicio.duracionSegundos ? `
                                <span class="badge bg-primary-subtle text-primary ms-2">
                                    <i class="fas fa-clock me-1"></i>${formatearDuracion(ejercicio.duracionSegundos)}
                                </span>
                            ` : ''}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}"
                         data-bs-parent="#accordionEjercicios">
                        <div class="accordion-body">
                            <ol class="mb-0">
                                ${instrucciones.map((paso) => `<li class="mb-2">${window.CampusCareApi.escapeHtml(paso)}</li>`).join('')}
                            </ol>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
});