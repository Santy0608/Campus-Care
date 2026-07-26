document.addEventListener('DOMContentLoaded', () => {
    const api = window.CampusCareApi;
    if (!api?.requireRole('admin')) return;

    const ui = {
        tabla: document.getElementById('recursosBody'),
        mensaje: document.getElementById('recursosMensaje'),
        formulario: document.getElementById('formBusqueda'),
        buscar: document.getElementById('campoBuscar'),
        limpiar: document.getElementById('limpiarBusqueda'),
    };
    const estado = { recursos: [], termino: '' };

    ui.formulario.addEventListener('submit', (event) => {
        event.preventDefault();
        estado.termino = ui.buscar.value.trim().toLocaleLowerCase('es');
        renderizar();
    });

    ui.buscar.addEventListener('search', () => {
        if (!ui.buscar.value) limpiarBusqueda();
    });
    ui.limpiar.addEventListener('click', limpiarBusqueda);
    ui.tabla.addEventListener('click', (event) => {
        const boton = event.target.closest('[data-accion="eliminar"]');
        if (boton) eliminarRecurso(boton.dataset.id);
    });

    async function cargarRecursos() {
        mostrarEstado('Cargando recursos...');
        try {
            const respuesta = await api.request('/api/recursos/listado-recursos');
            estado.recursos = Array.isArray(respuesta) ? respuesta : [];
            renderizar();
        } catch (error) {
            mostrarMensaje(error.message || 'No se pudieron cargar los recursos.', 'danger');
            mostrarEstado('No se pudieron cargar los recursos.');
        }
    }

    function filtrarRecursos() {
        if (!estado.termino) return estado.recursos;

        return estado.recursos.filter((recurso) => [
            recurso.titulo,
            recurso.contenido,
            recurso.categoriaNombre,
            recurso.tipoRecursoNombre,
            recurso.urlEnlace,
        ].some((valor) => String(valor ?? '')
            .toLocaleLowerCase('es')
            .includes(estado.termino)));
    }

    function limpiarBusqueda() {
        ui.buscar.value = '';
        estado.termino = '';
        renderizar();
        ui.buscar.focus();
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
            cancelButtonText: 'Cancelar',
        });
        if (!resultado.isConfirmed) return;

        try {
            await api.request(`/api/recursos/eliminar-recurso/${encodeURIComponent(id)}`, {
                method: 'DELETE',
            });
            estado.recursos = estado.recursos.filter(
                (recurso) => String(recurso.id) !== String(id),
            );
            renderizar();
            mostrarMensaje('Recurso eliminado correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message || 'No se pudo eliminar el recurso.', 'danger');
        }
    }

    function formatearFecha(fecha) {
        if (!fecha) return '';
        const valor = new Date(fecha);
        return Number.isNaN(valor.getTime()) ? String(fecha) : valor.toLocaleString('es-CR');
    }

    function obtenerUrlSegura(url) {
        if (!url) return null;
        try {
            const valor = new URL(url, window.location.origin);
            return ['http:', 'https:'].includes(valor.protocol) ? valor.href : null;
        } catch {
            return null;
        }
    }

    function celda(texto = '') {
        const elemento = document.createElement('td');
        elemento.textContent = texto ?? '';
        return elemento;
    }

    function crearFila(recurso) {
        const fila = document.createElement('tr');
        fila.append(
            celda(recurso.id),
            celda(recurso.titulo),
            celda(recurso.tipoRecursoNombre),
            celda(recurso.categoriaNombre),
        );

        const celdaUrl = celda('Sin enlace');
        const url = obtenerUrlSegura(recurso.urlEnlace);
        if (url) {
            const enlace = document.createElement('a');
            enlace.href = url;
            enlace.target = '_blank';
            enlace.rel = 'noopener noreferrer';
            enlace.textContent = 'Abrir';
            celdaUrl.replaceChildren(enlace);
        }

        fila.append(
            celdaUrl,
            celda(formatearFecha(recurso.fechaPublicacion)),
            celda(recurso.activo ? 'Sí' : 'No'),
        );

        const acciones = celda();
        const actualizar = document.createElement('a');
        actualizar.href = `actualizar_recurso.html?id_recurso=${encodeURIComponent(recurso.id ?? '')}`;
        actualizar.className = 'btn btn-warning btn-sm me-1';
        actualizar.textContent = 'Actualizar';

        const eliminar = document.createElement('button');
        eliminar.type = 'button';
        eliminar.className = 'btn btn-danger btn-sm';
        eliminar.dataset.accion = 'eliminar';
        eliminar.dataset.id = recurso.id ?? '';
        eliminar.textContent = 'Eliminar';
        acciones.append(actualizar, eliminar);
        fila.append(acciones);
        return fila;
    }

    function renderizar() {
        const recursos = filtrarRecursos();
        ui.limpiar.classList.toggle('d-none', !estado.termino);
        if (!recursos.length) {
            mostrarEstado(estado.termino
                ? 'No se encontraron recursos.'
                : 'No hay recursos disponibles.');
            return;
        }
        ui.tabla.replaceChildren(...recursos.map(crearFila));
    }

    function mostrarEstado(texto) {
        const fila = document.createElement('tr');
        const contenido = celda(texto);
        contenido.colSpan = 8;
        contenido.className = 'text-center text-muted';
        fila.append(contenido);
        ui.tabla.replaceChildren(fila);
    }

    function mostrarMensaje(texto, tipo) {
        ui.mensaje.className = `alert alert-${tipo} mt-3`;
        ui.mensaje.textContent = texto;
    }

    cargarRecursos();
});
