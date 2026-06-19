/**
 * indexLogin.js
 * Maneja el formulario de inicio de sesión.
 * Si ya hay sesión activa, redirige al inicio.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Si ya hay sesión, redirigir al inicio
    if (sessionStorage.getItem('usuario')) {
        window.location.href = '../index.html';
        return;
    }

    const form    = document.getElementById('form-login');
    const msgArea = document.getElementById('mensaje-login');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const usuario    = form.nombre_usuario.value.trim();
            const contrasenia = form.contrasenia.value.trim();

            if (!usuario || !contrasenia) {
                mostrarError(msgArea, 'Por favor complete todos los campos.');
                return;
            }

            try {
                /*
                 * TODO: apuntar a tu endpoint real de autenticación.
                 * El endpoint debe recibir { nombre_usuario, contrasenia }
                 * y devolver { ok: true, usuario: { id, nombre, role } }
                 * o { ok: false, mensaje: '...' }.
                 */
                const resp = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre_usuario: usuario, contrasenia })
                });

                const data = await resp.json();

                if (data.ok) {
                    // Guardar sesión en sessionStorage
                    sessionStorage.setItem('usuario', JSON.stringify(data.usuario));
                    window.location.href = '../index.html';
                } else {
                    mostrarError(msgArea, data.mensaje || 'Usuario o contraseña incorrectos.');
                }
            } catch (err) {
                console.error('Error de login:', err);
                mostrarError(msgArea, 'No se pudo conectar con el servidor. Intenta de nuevo.');
            }
        });
    }
});

function mostrarError(container, texto) {
    if (!container) return;
    container.innerHTML = `<p class="mensaje-error">${escapeHtml(texto)}</p>`;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
