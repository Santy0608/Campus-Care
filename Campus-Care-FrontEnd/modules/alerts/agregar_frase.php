<?php

include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');
include ('../../includes/header.php');


$descripcion = $activo = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    if (empty($_POST['descripcion'])){
        $errores['descripcion'] = 'La descripción es obligatoria';
    } else {
        $descripcion = trim($_POST['descripcion']);
    }

    $fecha_publicacion = date('Y-m-d H:i:s');
    $activo = trim($_POST['activo']);

    if (empty($errores)){
        try {
            $sql = 'INSERT INTO frases (descripcion, fecha_publicacion, activo) 
                    VALUES (:descripcion, :fecha_publicacion, :activo)';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'descripcion' => $descripcion,
                'fecha_publicacion' => $fecha_publicacion,
                'activo' => $activo
            ]);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Frase agregada',
                    text: 'La frase se ha agregado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = '/modules/alerts/agregar_frase.php';
                });
            </script>";
            exit;
        } catch (PDOException $e){
            echo 'Error: ' . $e->getMessage();
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
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        backdrop-filter: blur(6px);
        transition: all 0.25s ease;
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
    <h2>Agregar Frase</h2>

    <form method="post" action="agregar_frase.php">
        
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input 
                type="text" 
                class="form-control" 
                name="descripcion" 
                id="descripcion"
                value="<?php echo htmlspecialchars($descripcion ?? ''); ?>" 
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
                <option value="0" <?php echo ($activo ?? 1) == 0 ? 'selected' : ''; ?>>
                    Inactivo (Borrador)
                </option>
            </select>
        </div>

        <div class="d-flex gap-3">
            <button class="btn btn-success" type="submit">Agregar Frase</button>
            <a href="/modules/alerts/listado_frases.php" class="btn btn-success">Regresar</a>
        </div>
    </form>
</div>

<?php include ('../../includes/footer.php'); ?>
