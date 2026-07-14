document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('actualizarRecursoForm');
    const mensaje = document.getElementById('recursoMensaje');
    const selectTipo = document.getElementById('tipoRecursoId');
    const selectCategoria = document.getElementById('categoriaId');
    const params = new URLSearchParams(window.location.search);
    const idRecurso = params.get('id_recurso');

    if (!idRecurso) {
        mostrarMensaje('No se especificó el recurso a editar.', 'danger');
        form.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    async function cargarFormulario() {
        try {
            const [tipos, categorias, recurso] = await Promise.all([
                window.CampusCareApi.request('/api/tipos-recurso/listado-tipos-recurso'),
                window.CampusCareApi.request('/api/categorias/listado-categorias'),
                window.CampusCareApi.request(`/api/recursos/${encodeURIComponent(idRecurso)}`),
            ]);

            llenarSelect(selectTipo, tipos, recurso.tipoRecursoId);
            llenarSelect(selectCategoria, categorias, recurso.categoriaId);

            document.getElementById('titulo').value = recurso.titulo || '';
            document.getElementById('contenido').value = recurso.contenido || '';
            document.getElementById('url_enlace').value = recurso.urlEnlace || '';
            document.getElementById('activo').value = recurso.activo ? 'true' : 'false';
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            form.querySelector('button[type="submit"]').disabled = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            titulo: document.getElementById('titulo').value.trim(),
            tipoRecursoId: selectTipo.value,
            categoriaId: selectCategoria.value,
            contenido: document.getElementById('contenido').value.trim(),
            urlEnlace: document.getElementById('url_enlace').value.trim(),
            activo: document.getElementById('activo').value === 'true',
        };

        if (!payload.titulo || !payload.tipoRecursoId || !payload.categoriaId || !payload.contenido || !payload.urlEnlace) {
            mostrarMensaje('Complete todos los campos obligatorios.', 'warning');
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/recursos/actualizar-recurso/${encodeURIComponent(idRecurso)}`, {
                method: 'PUT',
                body: payload,
            });

            mostrarMensaje('Recurso actualizado correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function llenarSelect(select, opciones, seleccionado) {
        const items = Array.isArray(opciones) ? opciones : [];
        select.innerHTML = '<option value="">Seleccione una opcion</option>'
            + items.map((item) => {
                const isSelected = item.id === seleccionado ? ' selected' : '';
                return `<option value="${window.CampusCareApi.escapeHtml(item.id || '')}"${isSelected}>${window.CampusCareApi.escapeHtml(item.nombre || '')}</option>`;
            }).join('');
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarFormulario();
});