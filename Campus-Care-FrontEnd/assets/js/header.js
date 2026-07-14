/**
 * header.js
 * Renderiza el navbar dinámicamente según el estado de sesión
 * del usuario almacenado en sessionStorage/localStorage.
 *
 * Se espera que auth.js (o el módulo de autenticación) guarde:
 *   sessionStorage.setItem('usuario', JSON.stringify({ nombre, role }))
 * y lo limpie al cerrar sesión.
 */

document.addEventListener('DOMContentLoaded', () => {
    const usuario = window.CampusCareApi ? window.CampusCareApi.getStoredUser() : null;
    renderNavbar(usuario);
});

/**
 * Construye y monta el navbar dentro de <nav id="main-navbar">.
 * @param {object|null} usuario - { nombre, role } o null si no hay sesión
 */
function renderNavbar(usuario) {
    const nav = document.getElementById('main-navbar');
    if (!nav) return;

    // ------ Links según rol ------
    let roleLinks = '';

    if (usuario && usuario.role === 'estudiante') {
        roleLinks = `
            <li class="nav-item">
                <a class="nav-link" href="/modules/autoevaluacion/autoevaluacion.html" title="Evalua tu estado emocional">
                    <i class="fas fa-heart me-1"></i>Autoevaluación
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/modules/dashboard/dashboard_progreso.html" title="Ve tu progreso emocional">
                    <i class="fas fa-chart-line me-1"></i>Mi Progreso
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/modules/diario/indexDiario.html" title="Registra tus reflexiones">
                    <i class="fas fa-book-open me-1"></i>Mi Diario
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/modules/resources_student/recursos_estudiantes.html" title="Recursos de bienestar">
                    <i class="fas fa-seedling me-1"></i>Mis Recursos
                </a>
            </li>`;
    } else if (usuario && usuario.role === 'admin') {
        roleLinks = `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar recursos">
                    <i class="fas fa-spa me-1"></i>Recursos (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/modules/resources/listado_recursos.html">
                        <i class="fas fa-list me-2"></i>Ver todos los recursos
                    </a></li>
                    <li><a class="dropdown-item" href="/modules/resources/agregar_recurso.html">
                        <i class="fas fa-plus me-2"></i>Agregar nuevo recurso
                    </a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar usuarios">
                    <i class="fas fa-users me-1"></i>Usuarios (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/modules/admin/listado_estudiantes.html">
                        <i class="fas fa-graduation-cap me-2"></i>Ver estudiantes
                    </a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar frases">
                    <i class="fas fa-quote-right me-1"></i>Frases (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/modules/alerts/listado_frases.html">
                        <i class="fas fa-list me-2"></i>Ver todas las frases
                    </a></li>
                    <li><a class="dropdown-item" href="/modules/alerts/agregar_frase.html">
                        <i class="fas fa-plus me-2"></i>Agregar nueva frase
                    </a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/modules/dashboard_admin/dashboard_admin.html" title="Dashboard">
                    <i class="fas fa-chart-line me-1"></i>Dashboard
                </a>
            </li>`;
    }

    // ------ Link de sesión (saludo / login) ------
    let sessionLink = '';
    if (usuario) {
        const nombreSeguro = escapeHtml(usuario.nombre || usuario.username || 'Usuario');
        sessionLink = `
            <li class="nav-item dropdown d-flex align-items-center">
                <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown"
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-smile me-1"></i> Hola, ${nombreSeguro}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-user-edit me-2"></i>Editar perfil
                    </a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="btn-logout" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                </a>
            </li>`;
    } else {
        sessionLink = `
            <li class="nav-item">
                <a class="nav-link" href="/" title="Acceder a tu cuenta">
                    <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                </a>
            </li>`;
    }

    // ------ Inyectar HTML ------
    nav.innerHTML = `
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-leaf me-2"></i>Bienestar Estudiantil
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    ${roleLinks}
                    ${sessionLink}
                </ul>
            </div>
        </div>`;

    // Manejar logout
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', (e) => {
            e.preventDefault();
            cerrarSesion();
        });
    }
}

/**
 * Limpia la sesión y redirige al login.
 */
function cerrarSesion() {
    if (window.CampusCareApi) {
        window.CampusCareApi.clearSession();
    } else {
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('usuario');
    }
    window.location.href = '/';
}

/**
 * Escapa caracteres HTML para prevenir XSS.
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
