/**
 * registro.js
 * Maneja el formulario de registro de nuevos usuarios.
 *
 * FIX: antes usaba fetch('/api/usuarios/registrar-usuario', ...) con ruta
 * relativa — apuntaba al puerto de Live Server (5500), no al de Spring
 * Boot (8080). Ahora usa CampusCareApi.request(), que resuelve la URL
 * base correcta.
 *
 * FIX: CampusCareApi.request() solo agrega el header Authorization si
 * hay un token guardado — un usuario nuevo sin sesión nunca manda un
 * header "Bearer " vacío que tumbe la request contra el
 * JwtValidationFilter del backend.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form    = document.getElementById('form-registro');
    const msgArea = document.getElementById('mensaje-registro');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                nombre:         form.nombre.value.trim(),
                apellido:       form.apellido.value.trim(),
                telefono:       form.telefono.value.trim(),
                email:          form.email.value.trim(),
                nombreUsuario: form.nombreUsuario.value.trim(),
                contrasenia:    form.contrasenia.value.trim(),
                role:           'ESTUDIANTE'
            };

            const camposVacios = Object.values(payload).some(v => !v);
            if (camposVacios) {
                mostrarMensaje(msgArea, 'error', 'Por favor complete todos los campos.');
                return;
            }

            try {
                // POST /api/usuarios/registrar-usuario — pública (permitAll)
                await window.CampusCareApi.request('/api/usuarios/registrar-usuario', {
                    method: 'POST',
                    body: payload,
                });

                mostrarMensaje(msgArea, 'exito',
                    'Usuario registrado correctamente. <a href="indexLogin.html">Iniciar sesión</a>');
                form.reset();

            } catch (err) {
                console.error('Error de registro:', err);
                mostrarMensaje(msgArea, 'error', err.message || 'Error al registrar. Intenta de nuevo.');
            }
        });
    }
});

function mostrarMensaje(container, tipo, html) {
    if (!container) return;
    const clase = tipo === 'exito' ? 'mensaje-exito' : 'mensaje-error';
    container.innerHTML = `<p class="${clase}">${html}</p>`;
}