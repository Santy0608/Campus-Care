document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const estado = {
        usuarios: [],
        filtrados: [],
    };

    const form = document.getElementById('formBusqueda');
    const campoBuscar = document.getElementById('campoBuscar');
    const botonBuscar = document.getElementById('buscarBoton');
    const cuerpoTabla = document.getElementById('usuariosBody');
    const mensaje = document.getElementById('usuariosMensaje');

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        aplicarFiltro();
    });

    campoBuscar.addEventListener('input', () => {
        if (!campoBuscar.value.trim()) {
            botonBuscar.textContent = 'Buscar';
            estado.filtrados = [...estado.usuarios];
            renderUsuarios();
        }
    });

    async function cargarUsuarios() {
        try {
            const usuarios = await window.CampusCareApi.request('/api/usuarios/listado-usuarios');
            estado.usuarios = Array.isArray(usuarios) ? usuarios : [];
            estado.filtrados = [...estado.usuarios];
            renderUsuarios();
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            cuerpoTabla.innerHTML = '<tr><td colspan="6" class="text-muted">No se pudo cargar la lista de usuarios.</td></tr>';
        }
    }

    function aplicarFiltro() {
        const termino = campoBuscar.value.trim().toLowerCase();

        if (!termino) {
            estado.filtrados = [...estado.usuarios];
            botonBuscar.textContent = 'Buscar';
            renderUsuarios();
            return;
        }

        botonBuscar.textContent = 'Reestablecer';
        estado.filtrados = estado.usuarios.filter((usuarioItem) => {
            const texto = [
                usuarioItem.nombre,
                usuarioItem.apellido,
                usuarioItem.email,
                usuarioItem.nombreUsuario,
            ].join(' ').toLowerCase();

            return texto.includes(termino);
        });

        renderUsuarios();
    }

    botonBuscar.addEventListener('click', (event) => {
        if (botonBuscar.textContent.trim() === 'Reestablecer') {
            event.preventDefault();
            campoBuscar.value = '';
            botonBuscar.textContent = 'Buscar';
            estado.filtrados = [...estado.usuarios];
            renderUsuarios();
        }
    });

    function renderUsuarios() {
        if (!estado.filtrados.length) {
            cuerpoTabla.innerHTML = '<tr><td colspan="6" class="text-muted">No hay usuarios que coincidan con la búsqueda.</td></tr>';
            return;
        }

        cuerpoTabla.innerHTML = estado.filtrados.map((usuarioItem) => {
            const rol = usuarioItem.admin || (Array.isArray(usuarioItem.roles) && usuarioItem.roles.includes('ROLE_ADMIN'))
                ? 'Administrador'
                : 'Estudiante';

            return `
                <tr>
                    <td>${window.CampusCareApi.escapeHtml(usuarioItem.nombre || '')}</td>
                    <td>${window.CampusCareApi.escapeHtml(usuarioItem.apellido || '')}</td>
                    <td>${window.CampusCareApi.escapeHtml(usuarioItem.email || '')}</td>
                    <td>${window.CampusCareApi.escapeHtml(usuarioItem.nombreUsuario || '')}</td>
                    <td>${rol}</td>
                    <td>
                        <a href="editar_estudiantes.php?id_usuario=${encodeURIComponent(usuarioItem.id || '')}"
                           class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>`;
        }).join('');
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarUsuarios();
});