<?php

include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');
include ('../../includes/header.php');

$id_frase = $_GET['id_frase'] ?? null;

if (!$id_frase){
    header('Location: listado_frases.php');
    exit;
}

$descripcion = $activo = '';
$errores = [];

try {
    $stmt = $pdo->prepare('SELECT * FROM frases WHERE id_frase = :id_frase');
    $stmt->execute(['id_frase' => $id_frase]);
    $frase = $stmt->fetch();

    if (!$frase){
        echo "Frase no encontrada";
        exit;
    }

    $descripcion = $frase['descripcion'];
    $activo = $frase['activo'];

} catch (PDOException $e){
    echo "Error al obtener la frase: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_frase = $_POST['id_frase'] ?? $id_frase;

    if (empty($_POST['descripcion'])){
        $errores['descripcion'] = 'La descripción es obligatoria';
    } else {
        $descripcion = trim($_POST['descripcion']);
    }

    $fecha_publicacion = date('Y-m-d H:i:s');
    $activo = trim($_POST['activo']);

    if (empty($errores)) {
        try {
            $sql = 'UPDATE frases 
                    SET descripcion = :descripcion, 
                        fecha_publicacion = :fecha_publicacion, 
                        activo = :activo 
                    WHERE id_frase = :id_frase';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'descripcion' => $descripcion,
                'fecha_publicacion' => $fecha_publicacion,
                'activo' => $activo,
                'id_frase' => $id_frase
            ]);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Frase actualizada',
                    text: 'Los cambios se han guardado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar' 
                }).then(() => {   
                    window.location.href = '/modules/alerts/listado_frases.php';
                });
            </script>";
            exit;

        } catch (PDOException $e) {
            echo "Error al actualizar la frase: " . $e->getMessage();
        }
    }
}
?>

<link rel="stylesheet" href="../../assets/css/actualizar_frase.css">

<div class="form-wrapper">
    <h2>Actualizar Frase</h2>

    <form method="post" action="actualizar_frase.php?id_frase=<?php echo $id_frase; ?>">

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input 
                type="text" 
                id="descripcion" 
                name="descripcion" 
                class="form-control" 
                value="<?php echo htmlspecialchars($descripcion); ?>" 
                required
            >
            <?php if (isset($errores['descripcion'])): ?>
                <p class="error"><?php echo $errores['descripcion']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="activo" class="form-label">Estado</label>
            <select name="activo" class="form-select" id="activo">
                <option value="1">Activo (Publicado)</option>
                <option value="0" <?php echo $activo == 0 ? 'selected' : ''; ?>>
                    Inactivo (Borrador)
                </option>
            </select>
        </div>

        <input type="hidden" name="id_frase" value="<?php echo $id_frase; ?>">

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="/modules/alerts/listado_frases.php" class="btn btn-success">Regresar</a>
        </div>
    </form>
</div>

<?php include ('../../includes/footer.php'); ?>
