<?php
include '../../includes/auth.php';
validarAcceso('estudiante');

require '../../includes/db.php';

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/indexLogin.php");
    exit;
}

include('../../includes/header.php');

$id_usuario = $_SESSION['id_usuario']; 

// Obtener diario del usuario
try {
    $sql = "SELECT * FROM diario WHERE id_usuario = :id_usuario ORDER BY fecha DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al obtener tus entradas: " . $e->getMessage() . "</div>";
    exit;
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">📝 Mi Diario Emocional</h2>

    <?php if (empty($entradas)): ?>
        <div class="alert alert-info text-center">
            Aún no tienes entradas en tu diario. ¡Escribí tu primera reflexión del día! 🌱
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($entradas as $entrada): ?>
                <div class="list-group-item list-group-item-action mb-3 shadow-sm rounded">
                    <h5 class="mb-1 text-primary">
                        <?php echo date('d/m/Y', strtotime($entrada['fecha'])); ?>
                    </h5>
                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($entrada['contenido'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../../includes/footer.php'); ?>