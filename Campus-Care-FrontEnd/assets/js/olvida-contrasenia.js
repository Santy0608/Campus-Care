/**
 * olvida-contrasenia.js
 * Maneja el formulario de recuperación de contraseña.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form    = document.getElementById('form-olvida');
    const msgArea = document.getElementById('mensaje-olvida');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = form.email.value.trim();

            if (!email) {
                mostrarMensaje(msgArea, 'danger', 'Por favor ingresa tu correo electrónico.');
                return;
            }

            try {
                /*
                 * TODO: apuntar a tu endpoint real de recuperación de contraseña.
                 * Debe enviar un enlace al correo del usuario.
                 */
                const resp = await fetch('/api/recuperar-contrasenia', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const data = await resp.json();

                if (data.ok) {
                    mostrarMensaje(msgArea, 'success',
                        'Se ha enviado un enlace de recuperación a tu correo electrónico.');
                    form.reset();
                } else {
                    mostrarMensaje(msgArea, 'danger',
                        data.mensaje || 'No se encontró una cuenta con ese correo.');
                }
            } catch (err) {
                console.error('Error recuperación:', err);
                mostrarMensaje(msgArea, 'danger', 'No se pudo conectar con el servidor.');
            }
        });
    }
});

function mostrarMensaje(container, tipo, texto) {
    if (!container) return;
    container.innerHTML = `<div class="alert alert-${tipo}">${escapeHtml(texto)}</div>`;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
