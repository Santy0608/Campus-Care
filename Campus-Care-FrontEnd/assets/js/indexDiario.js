document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi.requireRole('estudiante');
    if (!usuario) return;

    const form = document.getElementById('formDiario');
    const mensaje = document.getElementById('mensajeDiario');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const contenido = form.contenido.value.trim();
        if (!contenido) {
            mostrarMensaje('warning', 'Por favor escribe algo antes de guardar.');
            return;
        }

        try {
            await window.CampusCareApi.request('/api/diarios/agregar-diario', {
                method: 'POST',
                body: {
                    idUsuario: usuario.id,
                    entradaTexto: contenido,
                },
            });

            mostrarMensaje('success', 'Tu entrada ha sido guardada correctamente 🌿');
            form.reset();
        } catch (error) {
            mostrarMensaje('danger', error.message || 'Error al guardar la entrada.');
        }
    });

    function mostrarMensaje(tipo, texto) {
        mensaje.innerHTML = `<div class="alert alert-${tipo} text-center mt-3">${window.CampusCareApi.escapeHtml(texto)}</div>`;
    }
});
