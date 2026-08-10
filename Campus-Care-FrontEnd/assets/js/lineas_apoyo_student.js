document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('estudiante');
    if (!usuario) return;

    const listaLineas = document.getElementById('listaLineas');
    const estadoVacio = document.getElementById('estadoVacio');
    const estadoError = document.getElementById('estadoError');

    try {
        const lineas = await window.CampusCareApi.request('/api/lineas-apoyo/listado-lineas-apoyo');
        const activas = Array.isArray(lineas) ? lineas.filter((l) => l.activo) : [];

        if (activas.length === 0) {
            estadoVacio.classList.remove('d-none');
            return;
        }

        renderLineas(activas);
    } catch (error) {
        console.error('Error cargando líneas de apoyo:', error);
        estadoError.textContent = 'No pudimos cargar las líneas de apoyo. Intenta de nuevo en unos minutos.';
        estadoError.classList.remove('d-none');
    }

    function renderLineas(lineas) {
        listaLineas.innerHTML = lineas.map((linea) => `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm linea-apoyo-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2">${window.CampusCareApi.escapeHtml(linea.nombreInstitucion || '')}</h5>

                        ${linea.telefono ? `
                            <p class="mb-1">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:${window.CampusCareApi.escapeHtml(linea.telefono)}" class="text-decoration-none">
                                    ${window.CampusCareApi.escapeHtml(linea.telefono)}
                                </a>
                            </p>
                        ` : ''}

                        ${linea.horarioAtencion ? `
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-clock me-2"></i>${window.CampusCareApi.escapeHtml(linea.horarioAtencion)}
                            </p>
                        ` : ''}

                        ${linea.urlSitio ? `
                            <a href="${window.CampusCareApi.escapeHtml(linea.urlSitio)}" target="_blank" rel="noopener"
                               class="btn btn-outline-primary btn-sm mt-auto align-self-start">
                                Visitar sitio <i class="fas fa-arrow-up-right-from-square ms-1"></i>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }
});