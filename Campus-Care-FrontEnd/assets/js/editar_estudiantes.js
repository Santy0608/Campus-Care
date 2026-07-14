document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('editarUsuarioForm');
    const mensaje = document.getElementById('usuarioMensaje');
    const params = new URLSearchParams(window.location.search);
    const idUsuario = params.get('id_usuario');

    if (!idUsuario) {
        mostrarMensaje('No se especificó el usuario a editar.', 'danger');
        form.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    async function cargarUsuario() {
        try {
            const usuarioActual = await window.CampusCareApi.request(`/api/usuarios/${encodeURIComponent(idUsuario)}`);
            document.getElementById('nombre').value = usuarioActual.nombre || '';
            document.getElementById('apellido').value = usuarioActual.apellido || '';
            document.getElementById('email').value = usuarioActual.email || '';
            document.getElementById('nombreUsuario').value = usuarioActual.nombreUsuario || '';
            document.getElementById('role').value = usuarioActual.admin ? 'admin' : 'estudiante';
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            form.querySelector('button[type="submit"]').disabled = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            nombre: document.getElementById('nombre').value.trim(),
            apellido: document.getElementById('apellido').value.trim(),
            email: document.getElementById('email').value.trim(),
            nombreUsuario: document.getElementById('nombreUsuario').value.trim(),
            contrasenia: document.getElementById('contrasenia').value,
            admin: document.getElementById('role').value === 'admin',
        };

        if (!payload.nombre || !payload.apellido || !payload.email || !payload.nombreUsuario) {
            mostrarMensaje('Complete todos los campos obligatorios.', 'warning');
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/usuarios/actualizar-usuario/${encodeURIComponent(idUsuario)}`, {
                method: 'PUT',
                body: payload,
            });

            mostrarMensaje('Usuario actualizado correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarUsuario();
});