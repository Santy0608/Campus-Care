/**
 * index.js
 * Lógica de la página principal:
 *  - Modal de estado de ánimo (con throttle de 24 h vía localStorage)
 *  - Modal de recordatorio de autoevaluación (solo para estudiantes)
 */

// Diccionario de frases motivacionales por estado de ánimo
const frasesMotivacionales = {
    feliz: {
        titulo: '¡Fantástico! Sigue así. ✨',
        texto: 'Tu energía positiva es contagiosa. Recuerda anotar lo que te hizo feliz hoy para revivir este momento luego.'
    },
    calmado: {
        titulo: '¡Qué tranquilidad! Respira profundo. 🧘',
        texto: 'La calma es una fortaleza. Tómate un momento para apreciar este equilibrio y úsalo para enfrentar el resto del día.'
    },
    estresado: {
        titulo: 'Tómate un descanso. ☕',
        texto: 'Es válido sentirse abrumado. Recuerda que pequeños pasos son progreso. Tómate 5 minutos y concéntrate solo en tu respiración.'
    },
    triste: {
        titulo: 'No estás solo. 🫂',
        texto: 'Permítete sentir lo que sientes. Si necesitas apoyo, estamos aquí para escucharte. Mañana es una nueva oportunidad para empezar de nuevo.'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initMoodModal();
    initAutoevaluacionModal();
});

// ─── Modal de Estado de Ánimo ────────────────────────────────────────────────

function initMoodModal() {
    const modalElement = document.getElementById('modalEstadoAnimo');
    if (!modalElement) return;

    const moodSelection    = document.getElementById('mood-selection');
    const motivationPhrase = document.getElementById('motivation-phrase');
    const phraseTitle      = document.getElementById('phrase-title');
    const phraseText       = document.getElementById('phrase-text');
    const moodButtons      = document.querySelectorAll('.mood-btn');

    const LAST_SHOWN_KEY = 'last_shown_mood_modal';
    const ONE_DAY_MS     = 24 * 60 * 60 * 1000;

    const now        = Date.now();
    const lastShown  = localStorage.getItem(LAST_SHOWN_KEY);

    if (!lastShown || (now - Number(lastShown)) > ONE_DAY_MS) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        localStorage.setItem(LAST_SHOWN_KEY, now);
    }

    // Selección de ánimo
    moodButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            moodButtons.forEach(btn => btn.classList.remove('selected-mood'));
            e.currentTarget.classList.add('selected-mood');

            const mood = e.currentTarget.getAttribute('data-mood');
            const data = frasesMotivacionales[mood];

            phraseTitle.textContent = data.titulo;
            phraseText.textContent  = data.texto;

            moodSelection.style.opacity = '0';
            setTimeout(() => {
                moodSelection.style.display  = 'none';
                motivationPhrase.style.opacity = '0';
                motivationPhrase.style.display = 'block';
                setTimeout(() => { motivationPhrase.style.opacity = '1'; }, 50);
            }, 300);
        });
    });

    // Resetear modal al cerrarlo
    modalElement.addEventListener('hidden.bs.modal', () => {
        motivationPhrase.style.display = 'none';
        moodSelection.style.display    = 'block';
        moodSelection.style.opacity    = '1';
    });
}

// ─── Modal de Recordatorio de Autoevaluación ─────────────────────────────────

function initAutoevaluacionModal() {
    const modalElement = document.getElementById('modalAutoevaluacion');
    if (!modalElement) return;

    // Solo mostrar si el usuario es estudiante
    const usuario = getUsuarioSesion();
    if (!usuario || usuario.role !== 'estudiante') return;

    // Verificar flag de notificación (reemplaza la lógica de $mostrarNotificacion PHP)
    const mostrarNotificacion = obtenerFlagNotificacion();
    if (!mostrarNotificacion) return;

    const LAST_SHOWN_KEY = 'last_shown_autoevaluacion_modal';
    const ONE_DAY_MS     = 24 * 60 * 60 * 1000;

    const now       = Date.now();
    const lastShown = localStorage.getItem(LAST_SHOWN_KEY);

    if (!lastShown || (now - Number(lastShown)) > ONE_DAY_MS) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        localStorage.setItem(LAST_SHOWN_KEY, now);
    }
}

/**
 * Determina si se debe mostrar la notificación de autoevaluación.
 * En la versión PHP esto venía del servidor; aquí se puede obtener
 * vía API o dejarlo en true para mostrar siempre (comportamiento por defecto).
 * @returns {boolean}
 */
function obtenerFlagNotificacion() {
    // TODO: reemplazar con llamada a API real, por ejemplo:
    // const resp = await fetch('/api/notificacion-autoevaluacion');
    // const data = await resp.json();
    // return data.mostrar;
    return true;
}

/**
 * Recupera el usuario de la sesión almacenada en el navegador.
 * @returns {object|null}
 */
function getUsuarioSesion() {
    const raw = sessionStorage.getItem('usuario');
    if (!raw) return null;
    try {
        return JSON.parse(raw);
    } catch {
        return null;
    }
}
