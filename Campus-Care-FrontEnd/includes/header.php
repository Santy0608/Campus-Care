<?php
// Incluir configuración de rutas
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre_usuario = '';

// Verifica si existe la sesión del estudiante
if (isset($_SESSION['nombre'])) {
    $nombre_usuario = htmlspecialchars($_SESSION['nombre']);
}

// Determinar la ruta del CSS basada en la ubicación actual
// Calcular la profundidad desde la raíz del proyecto de forma más simple
$current_script = $_SERVER['SCRIPT_NAME'];
$css_path = 'assets/css/estilos.css'; // Default para raíz

// Si estamos en un módulo (contiene /modules/)
if (strpos($current_script, '/modules/') !== false) {
    $css_path = '../../assets/css/estilos.css';
}
// Si estamos en login
elseif (strpos($current_script, '/login/') !== false) {
    $css_path = '../assets/css/estilos.css';
}
// Si estamos en includes
elseif (strpos($current_script, '/includes/') !== false) {
    $css_path = '../assets/css/estilos.css';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BienestarEstudiantil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/f4f41a499c.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
   
   <style>
    
    .navbar,
    .dropdown-menu {
        z-index: 9999 !important;
    }

    /*Bajar el zindex del carusel*/
    .carousel,
    .carousel-item,
    .carousel-item img {
        z-index: 1 !important;
    }

    .dropdown-menu {
      border-radius: 10px;
      padding: 8px 0;
}

</style>

<head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <!-- Branding terapéutico -->
    <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
      <i class="fas fa-leaf me-2"></i>Bienestar Estudiantil 
    </a> 

    <!-- Botón hamburguesa para móviles -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
      aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?php echo url('index.php'); ?>">
            <i class="fas fa-home me-1"></i>Inicio
          </a>
        </li>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'estudiante'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo url('modules/autoevaluacion/autoevaluacion.php'); ?>" title="Evalúa tu estado emocional">
                <i class="fas fa-heart me-1"></i>Autoevaluación
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo url('modules/dashboard/dashboard_progreso.php'); ?>" title="Ve tu progreso emocional">
                <i class="fas fa-chart-line me-1"></i>Mi Progreso
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo url('modules/diario/indexDiario.php'); ?>" title="Registra tus reflexiones">
                <i class="fas fa-book-open me-1"></i>Mi Diario
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo url('modules/resources_student/recursos_estudiantes.php'); ?>" title="Recursos de bienestar">
                <i class="fas fa-seedling me-1"></i>Mis Recursos
              </a>
            </li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar recursos">
                    <i class="fas fa-spa me-1"></i>Recursos (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo url('modules/resources/listado_recursos.php'); ?>">
                      <i class="fas fa-list me-2"></i>Ver todos los recursos
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo url('modules/resources/agregar_recurso.php'); ?>">
                      <i class="fas fa-plus me-2"></i>Agregar nuevo recurso
                    </a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar usuarios">
                    <i class="fas fa-users me-1"></i>Usuarios (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo url('modules/admin/listado_estudiantes.php'); ?>">
                      <i class="fas fa-graduation-cap me-2"></i>Ver estudiantes
                    </a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" title="Gestionar frases inspiradoras">
                    <i class="fas fa-quote-right me-1"></i>Frases (Admin)
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo url('modules/alerts/listado_frases.php'); ?>">
                      <i class="fas fa-list me-2"></i>Ver todas las frases
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo url('modules/alerts/agregar_frase.php'); ?>">
                      <i class="fas fa-plus me-2"></i>Agregar nueva frase
                    </a></li>
                </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo url('modules/dashboard_admin/dashboard_admin.php'); ?>" title="Ve tu progreso emocional">
                <i class="fas fa-chart-line me-1"></i>Dashboard
              </a>
            </li>
        <?php endif; ?>

        <!-- Saludo personalizado -->
        <?php if (isset($_SESSION['role'])): ?>
          <li class="nav-item dropdown d-flex align-items-center">
            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-smile me-1"></i> Hola, <?php echo htmlspecialchars($nombre_usuario); ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="/login/editarPerfil.php"><i class="fas fa-user-edit me-2"></i>Editar perfil</a></li>
              
            </ul>
          </li>
        <?php endif; ?>

        <!-- Cerrar sesión -->
        <?php if (isset($_SESSION['role'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo url('login/logout.php'); ?>" title="Cerrar sesión de forma segura">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                </a>
            </li>
        <?php endif; ?>
        
        <!-- Iniciar sesión -->
        <?php if (!isset($_SESSION['role'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo url('login/indexLogin.php'); ?>" title="Acceder a tu cuenta">
              <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

</body>
</html>