const CATEGORIAS = [
    { metrica: 'ESTRES', label: '😣 Nivel de Estrés', icono: '😣', nombre: 'Estrés' },
    { metrica: 'ANSIEDAD', label: '😟 Nivel de Ansiedad', icono: '😟', nombre: 'Ansiedad' },
    { metrica: 'SUENO', label: '😴 Calidad del Sueño', icono: '😴', nombre: 'Sueño' },
    { metrica: 'ANIMO', label: '😊 Estado de Ánimo General', icono: '😊', nombre: 'Ánimo' },
    { metrica: 'RELACIONES', label: '💕 Calidad de Relaciones', icono: '💕', nombre: 'Relaciones' },
    { metrica: 'MOTIVACION', label: '💪 Nivel de Motivación', icono: '💪', nombre: 'Motivación' },
];


document.addEventListener('DOMContentLoaded', async () => {
    const usuario = window.CampusCareApi.requireRole('estudiante'); // ajustá el rol si aplica
    if (!usuario) return;

    const form = document.getElementById('formEvaluacion');
    const resumen = document.getElementById('resumenEvaluacion');
    const errorBox = document.getElementById('mensajeError');

    renderFormulario();
    await cargarEstadoHoy();

    function renderFormulario() {
        const container = document.getElementById('categoriasContainer');
        container.innerHTML = CATEGORIAS.map(cat => `
        <div class="category-card">
            <div class="category-label">${cat.label}</div>
            <div class="range-container">
                <div class="range-labels">
                    <span class="range-label">1 (Malo)</span>
                    <span class="range-label">5 (Excelente)</span>
                </div>
                <input type="range" class="form-range" min="1" max="5" value="3"
                    name="${cat.metrica}" id="${cat.metrica}">
                <div class="range-value">
                    <span class="range-value-number" id="value_${cat.metrica}">3</span>
                </div>
            </div>
        </div>
    `).join('');

        document.querySelectorAll('.form-range').forEach(range => {
            const update = () => {
                const valEl = document.getElementById('value_' + range.id);
                if (valEl) {
                    valEl.textContent = range.value;
                    const pct = ((range.value - range.min) / (range.max - range.min)) * 100;
                    range.style.setProperty('--thumb-position', pct + '%');
                }
                range.dataset.touched = 'true';
            };
            range.addEventListener('input', update);
            range.addEventListener('change', update);
        });

        document.querySelectorAll('.category-card').forEach((card, i) => {
            card.style.animationDelay = (i * 0.1) + 's';
            card.classList.add('fade-in');
        });
    }

    async function cargarEstadoHoy() {
        try {
            const response = await window.CampusCareApi.request(`/api/autoevaluaciones/hoy?usuarioId=${usuario.id}`);
            if (response) {
                mostrarResumen(response);
            } else {
                form.classList.remove('d-none');
            }
        } catch (error) {
            // Si no hay evaluación hoy (204 o error controlado), mostramos el form
            form.classList.remove('d-none');
        }
    }

    function mostrarResumen(datos) {
        document.getElementById('fechaEvaluacion').textContent =
            new Date(datos.fechaEvaluacion).toLocaleDateString('es-CR');

        document.getElementById('resumenScores').innerHTML = CATEGORIAS.map(cat => {
            const respuesta = datos.respuestas.find(r => r.metrica === cat.metrica);
            const score = respuesta ? respuesta.score : '-';
            return `
                <div class="col-6 col-md-2 mb-3">
                    <div class="evaluation-summary-item">
                        <div class="evaluation-icon">${cat.icono}</div>
                        <small class="text-muted d-block mb-1">${cat.nombre}</small>
                        <div class="evaluation-score">${score}<span class="text-muted">/5</span></div>
                    </div>
                </div>
            `;
        }).join('');

        resumen.classList.remove('d-none');
        form.classList.add('d-none');
    }


    document.getElementById('btnCorregir')?.addEventListener('click', () => {
        resumen.classList.add('d-none');
        form.classList.remove('d-none');
    });

    function validarFormulario() {
        let isValid = true;
        document.querySelectorAll('.form-range').forEach(range => {
            if (range.dataset.touched !== 'true') {
                isValid = false;
                range.style.borderColor = '#ff6b6b';
            } else {
                range.style.borderColor = '';
            }
        });

        if (!isValid) {
            errorBox.textContent = 'Por favor, ajustá todas las categorías antes de guardar.';
            errorBox.classList.remove('d-none');
        }

        return isValid;
    }

    // FIX: antes había DOS listeners de submit casi idénticos registrados con
    // addEventListener — no se reemplazan entre sí, se ACUMULAN. Cada submit
    // disparaba dos POST a /api/autoevaluaciones/guardar-autoevaluacion,
    // pudiendo crear dos documentos de autoevaluación el mismo día para el
    // mismo usuario (condición de carrera contra el chequeo yaEvaluoHoy del
    // backend) y duplicar los puntos otorgados. Se dejó un solo listener.
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errorBox.classList.add('d-none');

        if (!validarFormulario()) return;

        const payload = {
            idUsuario: usuario.id,
            respuestas: CATEGORIAS.map(cat => ({
                metrica: cat.metrica,
                score: parseInt(document.getElementById(cat.metrica).value, 10),
            })),
        };

        try {
            const resultado = await window.CampusCareApi.request('/api/autoevaluaciones/guardar-autoevaluacion', {
                method: 'POST',
                body: payload,
            });
            mostrarResumen(resultado);
            mostrarModalPuntos(resultado.puntosGanados);
        } catch (error) {
            errorBox.textContent = error.message || 'Ocurrió un error al guardar la evaluación.';
            errorBox.classList.remove('d-none');
        }
    });

    function mostrarModalPuntos(puntos) {
        const modalBody = document.getElementById('modal-felicitacion-body');
        modalBody.innerHTML = `
            <p class="lead">¡Has completado tu autoevaluación de hoy!</p>
            <div class="my-4">
                <span class="display-4 fw-bolder text-warning">+${puntos}</span>
                <p class="text-muted mb-0">puntos ganados</p>
            </div>
            <p class="h5">¡Seguí así para mantener tu racha! 🔥</p>
        `;
        const felicitacionModal = new bootstrap.Modal(document.getElementById('modal-felicitacion'));
        felicitacionModal.show();
    }

});