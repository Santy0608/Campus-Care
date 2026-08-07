document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('actualizarLineaApoyoForm');
    const mensaje = document.getElementById('lineaApoyoMensaje');
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    if (!id) {
        mostrarMensaje('No se especificó la línea de apoyo a editar.', 'danger');
        form.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    async function cargarLineaApoyo() {
        try {
            const linea = await window.CampusCareApi.request(`/api/lineas-apoyo/${encodeURIComponent(id)}`);
            document.getElementById('nombreInstitucion').value = linea.nombreInstitucion || '';
            document.getElementById('telefono').value = linea.telefono || '';
            document.getElementById('horarioAtencion').value = linea.horarioAtencion || '';
            document.getElementById('urlSitio').value = linea.urlSitio || '';
            document.getElementById('activo').value = linea.activo ? 'true' : 'false';
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            form.querySelector('button[type="submit"]').disabled = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            nombreInstitucion: document.getElementById('nombreInstitucion').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            horarioAtencion: document.getElementById('horarioAtencion').value.trim(),
            urlSitio: document.getElementById('urlSitio').value.trim(),
            activo: document.getElementById('activo').value === 'true',
        };

        if (!payload.nombreInstitucion || !payload.telefono) {
            mostrarMensaje('Debe completar al menos institución y teléfono.', 'warning');
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/lineas-apoyo/actualizar-linea-apoyo/${encodeURIComponent(id)}`, {
                method: 'PUT',
                body: payload,
            });

            mostrarMensaje('Línea de apoyo actualizada correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarLineaApoyo();
});