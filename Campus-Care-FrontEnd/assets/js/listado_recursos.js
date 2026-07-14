document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const estado = {
        recursos: [],
        filtrados: [],
    };

    const cuerpoTabla = document.getElementById('recursosBody');
    const mensaje = document.getElementById('recursosMensaje');
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
            estado.filtrados = [...estado.recursos];
            renderRecursos();
        }
    });

    async function cargarRecursos() {
        try {
            const recursos = await window.CampusCareApi.request('/api/recursos/listado-recursos');
            estado.recursos = Array.isArray(recursos) ? recursos : [];
            estado.filtrados = [...estado.recursos];
            renderRecursos();
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            cuerpoTabla.innerHTML = '<tr><td colspan="8" class="text-muted">No se pudieron cargar los recursos.</td></tr>';
        }
    }

    function aplicarFiltro() {
        const termino = campoBuscar.value.trim().toLowerCase();

        if (!termino) {
            estado.filtrados = [...estado.recursos];
            botonBuscar.textContent = 'Buscar';
            renderRecursos();
            return;
        }

        botonBuscar.textContent = 'Reestablecer';
        estado.filtrados = estado.recursos.filter((recurso) => {
            const texto = [
                recurso.titulo,
                recurso.contenido,
                recurso.categoriaNombre,
                recurso.tipoRecursoNombre,
                recurso.urlEnlace,
            ].join(' ').toLowerCase();

            return texto.includes(termino);
        });

        renderRecursos();
    }

    async function eliminarRecurso(id) {
        const resultado = await Swal.fire({
            title: '¿Eliminar recurso?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!resultado.isConfirmed) {
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/recursos/eliminar-recurso/${encodeURIComponent(id)}`, {
                method: 'DELETE'
            });

            estado.recursos = estado.recursos.filter((recurso) => recurso.id !== id);
            estado.filtrados = estado.filtrados.filter((recurso) => recurso.id !== id);
            renderRecursos();
            mostrarMensaje('Recurso eliminado correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return '';
        }

        const date = new Date(fecha);
        if (Number.isNaN(date.getTime())) {
            return fecha;
        }

        return date.toLocaleString('es-CR');
    }

    function renderRecursos() {
        if (!estado.filtrados.length) {
            cuerpoTabla.innerHTML = '<tr><td colspan="8" class="text-muted">No hay recursos disponibles.</td></tr>';
            return;
        }

        cuerpoTabla.innerHTML = estado.filtrados.map((recurso) => `
            <tr>
                <td>${window.CampusCareApi.escapeHtml(recurso.id || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(recurso.titulo || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(recurso.tipoRecursoNombre || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(recurso.categoriaNombre || '')}</td>
                <td><a href="${window.CampusCareApi.escapeHtml(recurso.urlEnlace || '#')}" target="_blank" rel="noreferrer">Abrir</a></td>
                <td>${window.CampusCareApi.escapeHtml(formatearFecha(recurso.fechaPublicacion))}</td>
                <td>${recurso.activo ? 'Sí' : 'No'}</td>
                <td>
                    <a href="actualizar_recurso.html?id_recurso=${encodeURIComponent(recurso.id || '')}"
                       class="btn btn-warning btn-sm">Actualizar</a>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${window.CampusCareApi.escapeHtml(recurso.id || '')}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        document.querySelectorAll('.btn-eliminar').forEach((boton) => {
            boton.addEventListener('click', () => eliminarRecurso(boton.dataset.id));
        });
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo} mt-3`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarRecursos();
});