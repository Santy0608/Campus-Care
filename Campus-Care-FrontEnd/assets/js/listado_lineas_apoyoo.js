document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const estado = {
        lineasApoyo: [],
        filtradas: [],
    };

    const cuerpoTabla = document.getElementById('lineaApoyoBody');
    const mensaje = document.getElementById('lineaApoyoMensaje');
    const form = document.getElementById('formBusqueda');
    const campoBuscar = document.getElementById('campoBuscar');
    const botonBuscar = document.getElementById('buscarBoton');

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        aplicarFiltro();
    });

    botonBuscar.addEventListener('click', (event) => {
        if (botonBuscar.textContent.trim() === 'Reestablecer') {
            event.preventDefault();
            campoBuscar.value = '';
            botonBuscar.textContent = 'Buscar';
            estado.filtradas = [...estado.lineasApoyo];
            renderLineas();
        }
    });

    cargarLineasApoyo();

    async function cargarLineasApoyo() {
        try {
            const lineasApoyo = await window.CampusCareApi.request('/api/lineas-apoyo/listado-lineas-apoyo');
            estado.lineasApoyo = Array.isArray(lineasApoyo) ? lineasApoyo : [];
            estado.filtradas = [...estado.lineasApoyo];
            renderLineas();
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            cuerpoTabla.innerHTML = '<tr><td colspan="6" class="text-muted">No se pudieron cargar las líneas de apoyo.</td></tr>';
        }
    }

    function aplicarFiltro() {
        const termino = campoBuscar.value.trim().toLowerCase();

        if (!termino) {
            estado.filtradas = [...estado.lineasApoyo];
            botonBuscar.textContent = 'Buscar';
            renderLineas();
            return;
        }

        botonBuscar.textContent = 'Reestablecer';
        estado.filtradas = estado.lineasApoyo.filter((lineaApoyo) => {
            const texto = [lineaApoyo.nombreInstitucion, lineaApoyo.telefono]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();
            return texto.includes(termino);
        });
        renderLineas();
    }

    function renderLineas() {
        if (!estado.filtradas.length) {
            cuerpoTabla.innerHTML = '<tr><td colspan="6" class="text-muted text-center">No hay líneas de apoyo para mostrar.</td></tr>';
            return;
        }

        cuerpoTabla.innerHTML = estado.filtradas.map((linea) => `
            <tr>
                <td>${window.CampusCareApi.escapeHtml(linea.nombreInstitucion || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(linea.telefono || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(linea.horarioAtencion || '')}</td>
                <td>${linea.urlSitio
                    ? `<a href="${window.CampusCareApi.escapeHtml(linea.urlSitio)}" target="_blank" rel="noopener">Abrir</a>`
                    : '<span class="text-muted">Sin enlace</span>'}</td>
                <td>${linea.activo ? 'Sí' : 'No'}</td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm" onclick="editarLinea('${linea.id}')">Actualizar</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarLinea('${linea.id}')">Eliminar</button>
                </td>
            </tr>
        `).join('');
    }

    function mostrarMensaje(texto, tipo) {
        if (!mensaje) return;
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    // Expuestas globalmente para los onclick inline de la tabla
    window.editarLinea = function (id) {
        window.location.href = `actualizar_linea_apoyo.html?id=${encodeURIComponent(id)}`;
    };

    window.eliminarLinea = async function (id) {
        if (!confirm('¿Seguro que querés eliminar esta línea de apoyo?')) return;

        try {
            await window.CampusCareApi.request(`/api/lineas-apoyo/eliminar-linea-apoyo/${encodeURIComponent(id)}`, {
                method: 'DELETE',
            });
            await cargarLineasApoyo();
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    };
});