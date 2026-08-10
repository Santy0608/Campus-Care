document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('agregarLineaApoyoForm');
    const mensaje = document.getElementById('lineaApoyoMensaje');

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
            await window.CampusCareApi.request('/api/lineas-apoyo/agregar-linea-apoyo', {
                method: 'POST',
                body: payload,
            });

            form.reset();
            document.getElementById('activo').value = 'true';
            mostrarMensaje('Línea de apoyo agregada correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }
});