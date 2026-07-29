/**
 * restablecer_contrasenia.js
 * Maneja el formulario para establecer una nueva contraseña a partir de un
 * token de recuperación recibido por correo (ver olvida-contrasenia.js).
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-restablecer');
    const mensaje = document.getElementById('mensajeRestablecer');
    const token = new URLSearchParams(window.location.search).get('token');

    if (!token) {
        mostrarMensaje('danger', 'El enlace de restablecimiento no es válido o ha expirado.');
        form.classList.add('d-none');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const contrasenia = form.contrasenia.value;
        const confirmar = form.confirmarContrasenia.value;

        if (contrasenia.length < 8) {
            mostrarMensaje('danger', 'La contraseña debe tener al menos 8 caracteres.');
            return;
        }

        if (contrasenia !== confirmar) {
            mostrarMensaje('danger', 'Las contraseñas no coinciden.');
            return;
        }

        try {
            /*
             * TODO: apuntar al endpoint real de restablecimiento de contraseña
             * una vez esté documentado/expuesto por el backend.
             */
            await window.CampusCareApi.request('/api/restablecer-contrasenia', {
                method: 'POST',
                body: { token, contrasenia },
            });

            mostrarMensaje('exito', 'Tu contraseña fue actualizada correctamente. Ya podés iniciar sesión.');
            form.reset();
            form.classList.add('d-none');
        } catch (error) {
            mostrarMensaje('danger', error.message || 'No se pudo restablecer la contraseña.');
        }
    });

    function mostrarMensaje(tipo, texto) {
        const clase = tipo === 'exito' ? 'mensaje-exito' : `alert alert-${tipo}`;
        mensaje.innerHTML = `<div class="${clase}">${window.CampusCareApi.escapeHtml(texto)}</div>`;
    }
});
