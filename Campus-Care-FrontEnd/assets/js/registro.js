/**
 * registro.js
 * Maneja el formulario de registro de nuevos usuarios.
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
                nombre_usuario: form.nombre_usuario.value.trim(),
                contrasenia:    form.contrasenia.value.trim(),
                role:           'estudiante'
            };

            const camposVacios = Object.values(payload).some(v => !v);
            if (camposVacios) {
                mostrarMensaje(msgArea, 'error', 'Por favor complete todos los campos.');
                return;
            }

            try {
                /*
                 * TODO: apuntar a tu endpoint real de registro.
                 * Debe devolver { ok: true } o { ok: false, mensaje: '...' }.
                 */
                const resp = await fetch('/api/registro', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await resp.json();

                if (data.ok) {
                    mostrarMensaje(msgArea, 'exito',
                        'Usuario registrado correctamente. <a href="indexLogin.html">Iniciar sesión</a>');
                    form.reset();
                } else {
                    mostrarMensaje(msgArea, 'error', data.mensaje || 'Error al registrar. Intenta de nuevo.');
                }
            } catch (err) {
                console.error('Error de registro:', err);
                mostrarMensaje(msgArea, 'error', 'No se pudo conectar con el servidor.');
            }
        });
    }
});

function mostrarMensaje(container, tipo, html) {
    if (!container) return;
    const clase = tipo === 'exito' ? 'mensaje-exito' : 'mensaje-error';
    container.innerHTML = `<p class="${clase}">${html}</p>`;
}
