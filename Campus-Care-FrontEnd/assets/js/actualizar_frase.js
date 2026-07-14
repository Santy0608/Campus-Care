document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('actualizarFraseForm');
    const mensaje = document.getElementById('fraseMensaje');
    const params = new URLSearchParams(window.location.search);
    const idFrase = params.get('id_frase');

    if (!idFrase) {
        mostrarMensaje('No se especificó la frase a editar.', 'danger');
        form.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    async function cargarFrase() {
        try {
            const frase = await window.CampusCareApi.request(`/api/frases-motivacionales/${encodeURIComponent(idFrase)}`);
            document.getElementById('texto').value = frase.texto || '';
            document.getElementById('autor').value = frase.autor || '';
            document.getElementById('activo').value = frase.activo ? 'true' : 'false';
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
            form.querySelector('button[type="submit"]').disabled = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            texto: document.getElementById('texto').value.trim(),
            autor: document.getElementById('autor').value.trim(),
            activo: document.getElementById('activo').value === 'true',
        };

        if (!payload.texto || !payload.autor) {
            mostrarMensaje('Debe completar todos los campos obligatorios.', 'warning');
            return;
        }

        try {
            await window.CampusCareApi.request(`/api/frases-motivacionales/actualizar-frase-motivacional/${encodeURIComponent(idFrase)}`, {
                method: 'PUT',
                body: payload,
            });

            mostrarMensaje('Frase actualizada correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    cargarFrase();
});