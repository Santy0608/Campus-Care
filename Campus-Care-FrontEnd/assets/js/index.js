/**
 * index.js
 * Lógica de la página principal:
 *  - Modal de estado de ánimo (con throttle de 24 h vía localStorage)
 *  - Modal de recordatorio de autoevaluación (solo para estudiantes)
 *  - Frase del día (vía API)
 *
 * NOTA: el auth check ya NO se hace con una función propia leyendo
 * sessionStorage a mano. Se usa window.CampusCareApi.getStoredUser(),
 * la misma fuente de verdad que usan las páginas de admin (api-client.js),
 * para evitar tener dos implementaciones del mismo concepto.
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
    cargarFraseDelDia();
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

    const now       = Date.now();
    const lastShown = localStorage.getItem(LAST_SHOWN_KEY);

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
            if (!data) return; // guard: data-mood desconocido

            phraseTitle.textContent = data.titulo;
            phraseText.textContent  = data.texto;

            moodSelection.style.opacity = '0';
            setTimeout(() => {
                moodSelection.style.display    = 'none';
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

    // Fuente única de verdad para el usuario logueado: la misma que usan
    // las páginas de admin. Si no existe CampusCareApi cargado en esta
    // página, tratamos al usuario como no logueado en vez de romper.
    const usuario = window.CampusCareApi?.getStoredUser?.();
    if (!usuario || usuario.role !== 'estudiante') return;

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
 * TODO: reemplazar con llamada real cuando exista el endpoint, ej:
 *   const data = await window.CampusCareApi.request('/api/notificacion-autoevaluacion');
 *   return data.mostrar;
 * @returns {boolean}
 */
function obtenerFlagNotificacion() {
    return true;
}

// ─── Frase del Día ────────────────────────────────────────────────────────────

/**
 * Carga la frase motivacional del día desde el backend y la pinta en
 * el <blockquote id="frase-dia">. Si falla o el endpoint todavía no
 * existe, deja un mensaje claro en vez de "Cargando..." infinito.
 *
 * TODO: confirmar la ruta real del endpoint con el backend. Se asume
 * algo como GET /api/frases-motivacionales/aleatoria que devuelve
 * { texto, autor }. Ajustar cuando esté confirmado.
 */
async function cargarFraseDelDia() {
    const el = document.getElementById('frase-dia');
    if (!el) return;

    if (!window.CampusCareApi?.request) {
        el.textContent = 'No se pudo cargar la frase del día.';
        return;
    }

    try {
        // TODO: confirmar ruta real del endpoint
        const frase = await window.CampusCareApi.request('/api/frases-motivacionales/aleatoria');
        el.textContent = frase.autor ? `"${frase.texto}" — ${frase.autor}` : frase.texto;
    } catch (error) {
        el.textContent = 'No se pudo cargar la frase del día.';
    }
}