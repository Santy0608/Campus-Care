/**
 * indexLogin.js
 * Maneja el formulario de inicio de sesión.
 * Si ya hay sesión activa, redirige al inicio.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Si ya hay sesión, redirigir al inicio
    if (sessionStorage.getItem('usuario') && sessionStorage.getItem('token')) {
        window.location.href = '/';
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
                const data = await window.CampusCareApi.request('/login', {
                    method: 'POST',
                    body: { nombreUsuario: usuario, contrasenia }
                });

                window.CampusCareApi.storeAuthSession(data);
                window.location.href = '/';
            } catch (err) {
                console.error('Error de login:', err);
                mostrarError(msgArea, err.message || 'No se pudo conectar con el servidor. Intenta de nuevo.');
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
