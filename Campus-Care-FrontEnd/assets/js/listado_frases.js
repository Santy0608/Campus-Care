document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const estado = {
        frases: [],
        filtradas: [],
    };

    const cuerpoTabla = document.getElementById('frasesBody');
    const mensaje = document.getElementById('frasesMensaje');
    const form = document.getElementById('formBusqueda');
    const campoBuscar = document.getElementById('campoBuscar');
    const botonBuscar = document.getElementById('buscarBoton');

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        aplicarFiltro();
    });

    botonBuscar.addEventListener('click', (event) => {
        if (botonBuscar.textContent.trim() === 'Reestablecer') {
            event.preventDefault();
            campoBuscar.value = '';
            botonBuscar.textContent = 'Buscar';
            estado.filtradas = [...estado.frases];
            renderFrases();
        }
    });

    async function cargarFrases() {
        try {
            const frases = await window.CampusCareApi.request('/api/frases-motivacionales/listado-frases-motivacionales');
            estado.frases = Array.isArray(frases) ? frases : [];
            estado.filtradas = [...estado.frases];
            renderFrases();
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            cuerpoTabla.innerHTML = '<tr><td colspan="5" class="text-muted">No se pudieron cargar las frases.</td></tr>';
        }
    }

    function aplicarFiltro() {
        const termino = campoBuscar.value.trim().toLowerCase();

        if (!termino) {
            estado.filtradas = [...estado.frases];
            botonBuscar.textContent = 'Buscar';
            renderFrases();
            return;
        }

        botonBuscar.textContent = 'Reestablecer';
        estado.filtradas = estado.frases.filter((frase) => {
            const texto = [frase.texto, frase.autor].join(' ').toLowerCase();
            return texto.includes(termino);
        });
        renderFrases();
    }

    async function eliminarFrase(id) {
        const resultado = await Swal.fire({
            title: '¿Eliminar frase?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!resultado.isConfirmed) {
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/frases-motivacionales/eliminar-frase-motivacional/${encodeURIComponent(id)}`, {
                method: 'DELETE'
            });

            estado.frases = estado.frases.filter((frase) => frase.id !== id);
            estado.filtradas = estado.filtradas.filter((frase) => frase.id !== id);
            renderFrases();
            mostrarMensaje('Frase eliminada correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    }

    function renderFrases() {
        if (!estado.filtradas.length) {
            cuerpoTabla.innerHTML = '<tr><td colspan="5" class="text-muted">No hay frases disponibles.</td></tr>';
            return;
        }

        cuerpoTabla.innerHTML = estado.filtradas.map((frase) => `
            <tr>
                <td>${window.CampusCareApi.escapeHtml(frase.id || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(frase.texto || '')}</td>
                <td>${window.CampusCareApi.escapeHtml(frase.autor || '')}</td>
                <td>${frase.activo ? 'Activa' : 'Inactiva'}</td>
                <td>
                    <a href="actualizar_frase.php?id_frase=${encodeURIComponent(frase.id || '')}" class="btn btn-warning btn-sm">Editar</a>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${window.CampusCareApi.escapeHtml(frase.id || '')}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        document.querySelectorAll('.btn-eliminar').forEach((boton) => {
            boton.addEventListener('click', () => eliminarFrase(boton.dataset.id));
        });
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo} mt-3`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarFrases();
});