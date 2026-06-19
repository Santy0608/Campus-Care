/**
 * agenda_cita.js
 * Muestra/oculta el formulario según sesión y maneja la validación.
 */

document.addEventListener('DOMContentLoaded', () => {
    const usuario = getUsuarioSesion();
    renderAgendaContent(usuario);
});

function getUsuarioSesion() {
    const raw = sessionStorage.getItem('usuario');
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
}

function renderAgendaContent(usuario) {
    const seccionFormulario  = document.getElementById('seccion-formulario');
    const seccionSinSesion   = document.getElementById('seccion-sin-sesion');
    const inputNombre        = document.getElementById('nombre');
    const inputFecha         = document.getElementById('fecha');

    if (usuario) {
        // Mostrar formulario, ocultar bloque de login
        seccionFormulario.classList.remove('d-none');
        seccionSinSesion.classList.add('d-none');

        // Pre-rellenar nombre con el de sesión
        if (inputNombre && usuario.nombre) {
            inputNombre.value = escapeHtml(usuario.nombre);
        }

        // Establecer fecha mínima = mañana
        if (inputFecha) {
            const manana = new Date();
            manana.setDate(manana.getDate() + 1);
            inputFecha.min = manana.toISOString().split('T')[0];
        }

        // Manejar envío del formulario
        const form = document.getElementById('form-cita');
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }
    } else {
        seccionFormulario.classList.add('d-none');
        seccionSinSesion.classList.remove('d-none');
    }
}

function handleFormSubmit(e) {
    e.preventDefault();

    const nombre  = document.getElementById('nombre').value.trim();
    const email   = document.getElementById('email').value.trim();
    const fecha   = document.getElementById('fecha').value.trim();
    const hora    = document.getElementById('hora').value.trim();
    const motivo  = document.getElementById('motivo').value.trim();
    const msgArea = document.getElementById('mensaje-cita');

    if (!nombre || !email || !fecha || !hora || !motivo) {
        mostrarMensaje(msgArea, 'danger',
            '<i class="fas fa-exclamation-triangle me-2"></i>Por favor completa todos los campos obligatorios.');
        return;
    }

    // TODO: reemplazar con llamada real a la API backend
    // await fetch('/api/citas', { method:'POST', body: JSON.stringify({...}) })
    mostrarMensaje(msgArea, 'success',
        '<i class="fas fa-check-circle me-2"></i>¡Solicitud enviada correctamente! Te contactaremos pronto para confirmar tu cita.');

    e.target.reset();
}

function mostrarMensaje(container, tipo, html) {
    if (!container) return;
    container.innerHTML = `<div class="alert alert-${tipo}">${html}</div>`;
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
