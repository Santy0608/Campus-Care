document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('agregarRecursoForm');
    const mensaje = document.getElementById('recursoMensaje');
    const selectTipo = document.getElementById('tipoRecursoId');
    const selectCategoria = document.getElementById('categoriaId');

    async function cargarOpciones() {
        try {
            const [tipos, categorias] = await Promise.all([
                window.CampusCareApi.request('/api/tipos-recurso/listado-tipos-recurso'),
                window.CampusCareApi.request('/api/categorias/listado-categorias'),
            ]);

            llenarSelect(selectTipo, tipos, 'Seleccione un tipo de recurso');
            llenarSelect(selectCategoria, categorias, 'Seleccione una categoria');
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
            await window.CampusCareApi.request('/api/recursos/agregar-recurso', {
                method: 'POST',
                body: payload,
            });

            form.reset();
            document.getElementById('activo').value = 'true';
            mostrarMensaje('Recurso agregado correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function llenarSelect(select, opciones, placeholder) {
        const items = Array.isArray(opciones) ? opciones : [];
        select.innerHTML = `<option value="">${placeholder}</option>`
            + items.map((item) => `<option value="${window.CampusCareApi.escapeHtml(item.id || '')}">${window.CampusCareApi.escapeHtml(item.nombre || '')}</option>`).join('');
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarOpciones();
});