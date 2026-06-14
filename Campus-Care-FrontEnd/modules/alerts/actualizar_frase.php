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

<style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', sans-serif;
    }

    .form-wrapper {
        background: rgba(255, 255, 255, 0.65);
        padding: 35px;
        border-radius: 18px;
        max-width: 900px;
        margin: auto;
        margin-top: 60px;
        backdrop-filter: blur(6px);
        transition: all 0.25s ease;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .form-wrapper:hover {
        transform: translateY(-6px) scale(1.015);
        box-shadow: 0 18px 35px rgba(0,0,0,0.28);
    }

    h2 {
        font-weight: 700;
        font-size: 1.9rem;
        color: #1f2d3d;
        text-align: center;
        margin-bottom: 25px;
    }

    label {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2d3d;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 14px;
        font-size: 1.02rem;
        border: 1px solid #c8c8c8;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #003366;
        box-shadow: 0 0 6px rgba(0,51,102,0.25);
    }

    .btn-success {
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 600;
    }

    .btn-success:hover {
        opacity: 0.85;
    }

    .error {
        color: red;
        font-size: 0.9rem;
    }
</style>

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
