document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('estudiante');
    if (!usuario) return;

    const contenedor = document.getElementById('listaEntradas');

    try {
        const diarios = await window.CampusCareApi.request('/api/diarios/listado-diarios');
        const entradas = (Array.isArray(diarios) ? diarios : [])
            .filter((entrada) => String(entrada.idUsuario) === String(usuario.id))
            .sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        renderizar(entradas);
    } catch (error) {
        contenedor.innerHTML = `<div class="alert alert-danger">Error al obtener tus entradas: ${window.CampusCareApi.escapeHtml(error.message || '')}</div>`;
    }

    function renderizar(entradas) {
        if (!entradas.length) {
            contenedor.innerHTML = `
                <div class="alert alert-info text-center">
                    Aún no tienes entradas en tu diario. ¡Escribí tu primera reflexión del día! 🌱
                </div>`;
            return;
        }

        const lista = document.createElement('div');
        lista.className = 'list-group';

        entradas.forEach((entrada) => {
            const item = document.createElement('div');
            item.className = 'list-group-item list-group-item-action mb-3 shadow-sm rounded';

            const fecha = document.createElement('h5');
            fecha.className = 'mb-1 text-primary';
            fecha.textContent = new Date(entrada.fecha).toLocaleDateString('es-CR');

            const texto = document.createElement('p');
            texto.className = 'mb-1';
            texto.style.whiteSpace = 'pre-line';
            texto.textContent = entrada.entradaTexto;

            item.append(fecha, texto);
            lista.append(item);
        });

        contenedor.replaceChildren(lista);
    }
});
