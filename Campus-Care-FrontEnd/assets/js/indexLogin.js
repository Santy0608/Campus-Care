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

            const nombreUsuario = form.nombre_usuario.value.trim();
            const contrasenia   = form.contrasenia.value.trim();

            if (!nombreUsuario || !contrasenia) {
                mostrarMensaje(msgArea, 'danger', 'Por favor complete todos los campos.');
                return;
            }

            try {
                
                const resp = await fetch('/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nombre_usuario: nombreUsuario,
                        contrasenia:    contrasenia
                    })
                });

                if (!resp.ok) {
                    mostrarMensaje(msgArea, 'danger', 'Usuario o contraseña incorrectos.');
                    return;
                }

                const data = await resp.json();

                
                sessionStorage.setItem('token',   data.token);
                sessionStorage.setItem('usuario', JSON.stringify({
                    id:     data.id,
                    nombre: data.nombre,
                    role:   data.role   //'ESTUDIANTE' o 'ADMIN'
                }));

                window.location.href = '../index.html';

            } catch (err) {
                console.error('Error de login:', err);
                mostrarMensaje(msgArea, 'danger', 'No se pudo conectar con el servidor. Intenta de nuevo.');
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
