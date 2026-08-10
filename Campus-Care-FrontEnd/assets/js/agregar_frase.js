document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('admin');
    if (!usuario) {
        return;
    }

    const form = document.getElementById('agregarFraseForm');
    const mensaje = document.getElementById('fraseMensaje');

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
            await window.CampusCareApi.request('/api/frases-motivacionales/agregar-frase-motivacional', {
                method: 'POST',
                body: payload,
            });

            form.reset();
            document.getElementById('activo').value = 'true';
            mostrarMensaje('Frase agregada correctamente.', 'success');
        } catch (error) {
            mostrarMensaje(error.message, 'danger');
        }
    });

    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo}`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }
});