<?php
require_once(__DIR__ . '/includes/db.php');
include(__DIR__ . '/includes/header.php');
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="fas fa-phone-alt fa-3x text-primary mb-3"></i>
                <h1 class="display-4 mb-3">Líneas de Apoyo</h1>
                <p class="lead text-muted">Recursos de ayuda disponibles las 24 horas del día, los 7 días de la semana</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-heart fa-2x text-danger mb-3"></i>
                            <h5 class="card-title">Línea de Crisis Emocional</h5>
                            <h4 class="text-primary mb-3">911</h4>
                            <p class="text-muted">Para emergencias de salud mental. Disponible 24/7</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-headset fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Teléfono de la Esperanza</h5>
                            <h4 class="text-primary mb-3">+506 2253-5439</h4>
                            <p class="text-muted">Apoyo psicológico gratuito y confidencial</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-university fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">Bienestar Estudiantil</h5>
                            <h4 class="text-primary mb-3">+506 1234-5678</h4>
                            <p class="text-muted">Apoyo académico y personal para estudiantes</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-comments fa-2x text-info mb-3"></i>
                            <h5 class="card-title">Chat de Apoyo Online</h5>
                            <h4 class="text-primary mb-3">WhatsApp</h4>
                            <p class="text-muted">
                                <a href="https://wa.me/50612345678" target="_blank" class="btn btn-outline-success">
                                    <i class="fab fa-whatsapp me-2"></i>Iniciar chat
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-5" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading">¿Necesitas ayuda inmediata?</h5>
                        <p class="mb-0">Si te encuentras en una situación de crisis, no dudes en contactar cualquiera de estos servicios. Tu bienestar es nuestra prioridad.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo url('index.php'); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>