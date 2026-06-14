<?php
include '../../includes/auth.php';
validarAcceso('admin');
require '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_GET['id_usuario'])) {
    echo "<div class='alert alert-danger text-center mt-4'>No se ha especificado ningún usuario.</div>";
    exit;
}

$id_usuario = intval($_GET['id_usuario']);
$mensaje = "";

try {
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, apellido, telefono, role FROM usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "<div class='alert alert-warning text-center mt-4'>Usuario no encontrado.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al cargar usuario: " . $e->getMessage() . "</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!empty($nombre) && !empty($apellido) && !empty($telefono)) {
        try {
            $update = $pdo->prepare("UPDATE usuarios 
                                     SET nombre = :nombre, apellido = :apellido, telefono = :telefono, 
                                        role = :role 
                                     WHERE id_usuario = :id_usuario");
            $update->bindParam(':nombre', $nombre);
            $update->bindParam(':apellido', $apellido);
            $update->bindParam(':telefono', $telefono);
            $update->bindParam(':role', $role);
            $update->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $update->execute();

            $mensaje = "<div class='alert alert-success text-center'>Usuario actualizado correctamente.</div>";

            $usuario['nombre'] = $nombre;
            $usuario['apellido'] = $apellido;
            $usuario['telefono'] = $telefono;
            $usuario['role'] = $role;

        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger text-center'>Error al actualizar usuario: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning text-center'>Por favor complete todos los campos obligatorios.</div>";
    }
}
?>

<link rel="stylesheet" href="../../assets/css/editar_estudiantes.css">

<div class="usuarios-card">

    <div class="text-center">
        <h1>Editar Usuario</h1>
    </div>

    <?= $mensaje ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" 
                   value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellido:</label>
            <input type="text" name="apellido" class="form-control" 
                   value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono:</label>
            <input type="text" name="telefono" class="form-control" 
                   value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol:</label>
            <select name="role" class="form-select">
                <option value="estudiante" <?= $usuario['role'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
                <option value="admin" <?= $usuario['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="listado_estudiantes.php" class="btn btn-secondary">Volver</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>

</div>

<?php include '../../includes/footer.php'; ?>
