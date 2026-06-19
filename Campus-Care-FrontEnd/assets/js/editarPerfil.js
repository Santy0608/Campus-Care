/**
 * editarPerfil.js
 * Carga los datos del perfil del usuario y procesa el formulario de edición.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const usuario = getUsuarioSesion();

    // Si no hay sesión, redirigir al login
    if (!usuario) {
        window.location.href = 'indexLogin.html';
        return;
    }

    await cargarDatosUsuario(usuario.id);

    const form    = document.getElementById('form-editar-perfil');
    const msgArea = document.getElementById('mensaje-perfil');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await guardarCambios(usuario.id, form, msgArea);
        });
    }
});

function getUsuarioSesion() {
    const raw = sessionStorage.getItem('usuario');
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
}

/**
 * Obtiene los datos actuales del usuario desde la API y los rellena en el formulario.
 */
async function cargarDatosUsuario(idUsuario) {
    try {
        /*
         * TODO: apuntar a tu endpoint real.
         * Debe devolver { telefono, email, nombre_usuario }.
         */
        const resp = await fetch(`/api/usuarios/${idUsuario}`);
        const data = await resp.json();

        if (data) {
            setVal('telefono',       data.telefono);
            setVal('email',          data.email);
            setVal('nombre_usuario', data.nombre_usuario);
        }
    } catch (err) {
        console.error('Error al cargar perfil:', err);
    }
}

/**
 * Envía los cambios del perfil a la API.
 */
async function guardarCambios(idUsuario, form, msgArea) {
    const payload = {
        telefono:       form.telefono.value.trim(),
        email:          form.email.value.trim(),
        nombre_usuario: form.nombre_usuario.value.trim()
    };

    try {
        /*
         * TODO: apuntar a tu endpoint real de actualización.
         */
        const resp = await fetch(`/api/usuarios/${idUsuario}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await resp.json();

        if (data.ok) {
            mostrarMensaje(msgArea, 'success', 'Perfil actualizado exitosamente.');
        } else {
            mostrarMensaje(msgArea, 'danger', data.mensaje || 'Error al actualizar perfil.');
        }
    } catch (err) {
        console.error('Error al guardar perfil:', err);
        mostrarMensaje(msgArea, 'danger', 'No se pudo conectar con el servidor.');
    }
}

function setVal(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value ?? '';
}

function mostrarMensaje(container, tipo, texto) {
    if (!container) return;
    container.innerHTML = `<div class="alert alert-${tipo}">${escapeHtml(texto)}</div>`;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
