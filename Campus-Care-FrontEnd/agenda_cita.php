<?php
require_once(__DIR__ . '/includes/db.php');
include(__DIR__ . '/includes/header.php');

$mensaje = '';

// Solo procesar si el usuario está logueado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id_usuario'])) {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if (!empty($nombre) && !empty($email) && !empty($fecha) && !empty($hora) && !empty($motivo)) {
        $mensaje = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>¡Solicitud enviada correctamente! Te contactaremos pronto para confirmar tu cita.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Por favor completa todos los campos obligatorios.</div>';
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                <h1 class="display-4 mb-3">Agenda tu Cita</h1>
                <p class="lead text-muted">Solicita una cita con nuestros profesionales de bienestar estudiantil</p>
            </div>

            <?php if ($mensaje): ?>
                <?= $mensaje ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['id_usuario'])): ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-2"></i>Nombre completo *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= $_SESSION['nombre'] ?? '' ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Correo electrónico *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Teléfono
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono">
                                </div>

                                <div class="col-md-6">
                                    <label for="fecha" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Fecha deseada *
                                    </label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="hora" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Hora preferida *
                                    </label>
                                    <select class="form-select" id="hora" name="hora" required>
                                        <option value="">Selecciona una hora</option>
                                        <option value="08:00">8:00 AM</option>
                                        <option value="09:00">9:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="15:00">3:00 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                        <option value="17:00">5:00 PM</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="tipo_cita" class="form-label">
                                        <i class="fas fa-stethoscope me-2"></i>Tipo de cita
                                    </label>
                                    <select class="form-select" id="tipo_cita" name="tipo_cita">
                                        <option value="psicologia">Psicología</option>
                                        <option value="orientacion">Orientación académica</option>
                                        <option value="bienestar">Bienestar estudiantil</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="motivo" class="form-label">
                                        <i class="fas fa-comment-alt me-2"></i>Motivo de la consulta *
                                    </label>
                                    <textarea class="form-control" id="motivo" name="motivo" rows="4" 
                                              placeholder="Describe brevemente el motivo de tu cita..." required></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacidad" required>
                                        <label class="form-check-label" for="privacidad">
                                            Acepto el manejo confidencial de mis datos personales *
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Solicitar Cita
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading">Información importante</h5>
                            <p class="mb-0">Las citas se confirman por correo electrónico en un plazo de 24-48 horas. Para emergencias, contacta nuestras líneas de apoyo disponibles 24/7.</p>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5 text-center">
                        <i class="fas fa-lock fa-3x text-warning mb-4"></i>
                        <h3 class="mb-3">Inicia sesión para agendar tu cita</h3>
                        <p class="text-muted mb-4">Necesitas estar registrado en nuestro sistema para solicitar una cita.</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="<?php echo url('login/indexLogin.php'); ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                            <a href="<?php echo url('login/registro.php'); ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?php echo url('index.php'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>