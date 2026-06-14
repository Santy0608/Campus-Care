<?php

require '../includes/db.php';
include ('../includes/header.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/indexLogin.php");
    exit();
}

$id = $_SESSION['id_usuario'];

$mensaje_exito = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $nombre_usuario = $_POST['nombre_usuario'];

    try {
        $query = "UPDATE usuarios 
                  SET telefono = ?, email = ?, nombre_usuario = ?
                  WHERE id_usuario = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$telefono, $email, $nombre_usuario, $id]);

        $mensaje_exito = "Perfil actualizado exitosamente.";

    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}

$stmt = $pdo->prepare("SELECT telefono, email, nombre_usuario FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Editar Perfil</h2>

    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success">
            <?php echo $mensaje_exito; ?>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Número de teléfono</label>
            <input type="text" class="form-control" name="telefono" required
                   value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" required
                   value="<?php echo htmlspecialchars($usuario['email']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre de usuario</label>
            <input type="text" class="form-control" name="nombre_usuario" required
                   value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="../../index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
