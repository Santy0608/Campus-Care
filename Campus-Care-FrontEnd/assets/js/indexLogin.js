/**
 * indexLogin.js
 * Maneja el formulario de inicio de sesión.
 * Si ya hay sesión activa, redirige al inicio.
 *
 * FIX: antes usaba fetch('/login', ...) con ruta relativa — apuntaba al
 * puerto de Live Server (5500), no al de Spring Boot (8080). Ahora usa
 * CampusCareApi.request(), que ya resuelve la URL base correcta.
 *
 * FIX: antes leía data.id, data.nombre, data.role de la respuesta del
 * login — pero el backend (JwtAuthenticationFilter) solo devuelve
 * { token, username, message }. data.role nunca existió, quedaba
 * undefined en silencio. CampusCareApi.storeAuthSession() decodifica el
 * rol directamente del JWT, que es donde realmente vive.
 *
 * FIX: la función mostrarMensaje() se llamaba pero nunca se definía en
 * este archivo (solo existía mostrarError, con otra firma). Se agregó
 * la definición correcta.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Si ya hay sesión, redirigir al inicio
    if (window.CampusCareApi.getStoredUser()) {
        window.location.href = '/index.html';
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
                mostrarMensaje(msgArea, 'error', 'Por favor complete todos los campos.');
                return;
            }

            try {
                const authResponse = await window.CampusCareApi.request('/login', {
                    method: 'POST',
                    body: {
                        nombreUsuario: nombreUsuario,
                        contrasenia:    contrasenia,
                    },
                });

                window.CampusCareApi.storeAuthSession(authResponse);
                window.location.href = '/includes/index.html';

            } catch (err) {
                console.error('Error de login:', err);

                if (err.status === 401 || err.status === 403) {
                    mostrarMensaje(msgArea, 'error', 'Usuario o contraseña incorrectos.');
                } else {
                    mostrarMensaje(msgArea, 'error', 'No se pudo conectar con el servidor. Intenta de nuevo.');
                }
            }
        });
    }
});

function mostrarMensaje(container, tipo, texto) {
    if (!container) return;
    const clase = tipo === 'exito' ? 'mensaje-exito' : 'mensaje-error';
    container.innerHTML = `<p class="${clase}">${window.CampusCareApi.escapeHtml(texto)}</p>`;
}