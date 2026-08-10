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


/**
 * Obtiene los datos actuales del usuario desde la API y los rellena en el formulario.
 */
async function cargarDatosUsuario(idUsuario) {
    try {
        const resp = await fetch(`/api/usuarios/${idUsuario}`, {
            headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        });

        if (!resp.ok) throw new Error('No se pudo cargar el perfil.');

        const data = await resp.json();
        setVal('telefono',       data.telefono);
        setVal('email',          data.email);
        setVal('nombre_usuario', data.nombre_usuario);

    } catch (err) {
        console.error('Error al cargar perfil:', err);
    }
}

/**
 * Envía los cambios del perfil al backend mediante un PUT.
 */
async function guardarCambios(idUsuario, form, msgArea) {
    const payload = {
        telefono:       form.telefono.value.trim(),
        email:          form.email.value.trim(),
        nombre_usuario: form.nombre_usuario.value.trim()
    };

   try {
        const resp = await fetch(`/api/usuarios/actualizar-usuario/${idUsuario}`, {
            method:  'PUT',
            headers: {
                'Content-Type':  'application/json',
                'Authorization': `Bearer ${sessionStorage.getItem('token')}`
            },
            body: JSON.stringify(payload)
        });

        if (resp.ok) {
            mostrarMensaje(msgArea, 'success', 'Perfil actualizado exitosamente.');
        } else {
            const err = await resp.json().catch(() => ({}));
            mostrarMensaje(msgArea, 'danger', err.mensaje || 'Error al actualizar perfil.');
        }

    } catch (err) {
        console.error('Error al guardar perfil:', err);
        mostrarMensaje(msgArea, 'danger', 'No se pudo conectar con el servidor.');
    }
}

/**
 * Elimina el perfil del usuario mediante un DELETE.
 */
function confirmarEliminacion(idUsuario) {
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
 
    // El botón de confirmación dentro del modal ejecuta el delete
    document.getElementById('btn-confirmar-eliminar').onclick = async () => {
        modal.hide();
        await eliminarUsuario(idUsuario);
    };
}
 
async function eliminarUsuario(idUsuario) {
    const msgArea = document.getElementById('mensaje-perfil');
 
    try {
        const resp = await fetch(`/api/usuarios/eliminar-usuario/${idUsuario}`, {
            method:  'DELETE',
            headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        });
 
        if (resp.ok) {
            // Limpiar sesión y redirigir al login tras eliminar
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('usuario');
            window.location.href = 'indexLogin.html';
        } else {
            mostrarMensaje(msgArea, 'danger', 'No se pudo eliminar la cuenta. Intenta de nuevo.');
        }
 
    } catch (err) {
        console.error('Error al eliminar usuario:', err);
        mostrarMensaje(msgArea, 'danger', 'No se pudo conectar con el servidor.');
    }
}

//Helpers
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
