<?php

include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');
include('../../includes/header.php');

$id_recurso = $_GET['id_recurso'] ?? null;

if (!$id_recurso){
    header('location: listado_recursos.php');
    exit;
}

$errores = [];

try {
    $stmt = $pdo->prepare('SELECT * FROM recursos WHERE id_recurso = :id_recurso');
    $stmt->execute(['id_recurso' => $id_recurso]);
    $recurso = $stmt->fetch();

    if (!$recurso){
        echo "Recurso no encontrado";
        exit;
    }

    $titulo            = $recurso['titulo'];
    $tipo              = $recurso['tipo'];
    $contenido         = $recurso['contenido'];
    $url_enlace        = $recurso['url_enlace'];
    $categoria         = $recurso['categoria'];
    $fecha_publicacion = $recurso['fecha_publicacion'];
    $activo            = $recurso['activo'];

} catch(PDOException $e){
    echo "Error al obtener el recurso " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $titulo     = trim($_POST['titulo']);
    $tipo       = trim($_POST['tipo']);
    $contenido  = trim($_POST['contenido']);
    $url_enlace = trim($_POST['url_enlace']);
    $categoria  = trim($_POST['categoria']);
    $activo     = trim($_POST['activo']);
    $fecha_publicacion = date('Y-m-d H:i:s');

    if (!$titulo)     $errores['titulo'] = 'El título es obligatorio';
    if (!$tipo)       $errores['tipo'] = 'El tipo es obligatorio';
    if (!$contenido)  $errores['contenido'] = 'El contenido es obligatorio';
    if (!$url_enlace) $errores['url_enlace'] = 'La URL es obligatoria';
    if (!$categoria)  $errores['categoria'] = 'La categoría es obligatoria';

    if (empty($errores)){
        try {
            $sql = "UPDATE recursos 
                    SET titulo = :titulo,
                        tipo = :tipo,
                        contenido = :contenido,
                        url_enlace = :url_enlace,
                        categoria = :categoria,
                        fecha_publicacion = :fecha_publicacion,
                        activo = :activo
                    WHERE id_recurso = :id_recurso";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'titulo' => $titulo,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'url_enlace' => $url_enlace,
                'categoria' => $categoria,
                'fecha_publicacion' => $fecha_publicacion,
                'activo' => $activo,
                'id_recurso' => $id_recurso
            ]);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Recurso actualizado',
                    text: 'Los cambios se han guardado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = '/modules/resources/listado_recursos.php';
                });
            </script>";
            exit;

        } catch(PDOException $e){
            echo "Error al actualizar el recurso: " . $e->getMessage();
        }
    }
}

?>

<style>
body {
    background-image: url('../../assets/images/recursos.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Segoe UI', sans-serif;
}

.form-wrapper {
    background: rgba(255, 255, 255, 0.60);
    backdrop-filter: blur(12px);
    padding: 35px;
    border-radius: 18px;
    max-width: 1100px;
    margin: auto;
    margin-top: 60px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.20);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.form-wrapper:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.25);
}

h2 {
    font-weight: 700;
    color: #1f2d3d;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.9);
    margin-bottom: 25px;
    text-align: center;
}

label {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2d3d;
}

.form-control, .form-select, textarea {
    border-radius: 12px;
    padding: 14px;
    font-size: 1rem;
    border: 1px solid #c8c8c8;
    transition: all 0.2s ease;
}

.form-control:focus, 
.form-select:focus, 
textarea:focus {
    border-color: #003366;
    box-shadow: 0 0 6px rgba(0,51,102,0.25);
}

textarea {
    resize: vertical;
}

.btn-success {
    border-radius: 10px;
    padding: 10px 25px;
    font-size: 1rem;
    font-weight: 600;
}
</style>

<div class="form-wrapper">

    <h2>Actualizar Recurso</h2>

    <form method="post">

        <div class="mb-3">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" class="form-control"
                   value="<?= htmlspecialchars($titulo); ?>">
            <?php if(isset($errores['titulo'])): ?>
                <p class="error"><?= $errores['titulo']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="tipo">Tipo de Recurso</label>
            <select id="tipo" name="tipo" class="form-select">
                <option value="">-- Seleccione el Tipo --</option>
                <option value="articulo"  <?= $tipo === 'articulo' ? 'selected' : '' ?>>Artículo de Texto</option>
                <option value="video"     <?= $tipo === 'video' ? 'selected' : '' ?>>Video Embebido</option>
                <option value="ejercicio" <?= $tipo === 'ejercicio' ? 'selected' : '' ?>>Ejercicio Interactivo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria" class="form-select">
                <option value="">-- Seleccione la Categoría --</option>
                <option value="estres"         <?= $categoria === 'estres' ? 'selected' : '' ?>>Estrés</option>
                <option value="ansiedad"       <?= $categoria === 'ansiedad' ? 'selected' : '' ?>>Ansiedad</option>
                <option value="sueno"          <?= $categoria === 'sueno' ? 'selected' : '' ?>>Sueño</option>
                <option value="estado_animo"   <?= $categoria === 'estado_animo' ? 'selected' : '' ?>>Estado de Ánimo</option>
                <option value="relaciones"     <?= $categoria === 'relaciones' ? 'selected' : '' ?>>Relaciones</option>
                <option value="motivacion"     <?= $categoria === 'motivacion' ? 'selected' : '' ?>>Motivación</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="contenido">Contenido</label>
            <textarea id="contenido" name="contenido" rows="8" class="form-control"><?= htmlspecialchars($contenido); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="url_enlace">URL o Enlace</label>
            <input type="url" id="url_enlace" name="url_enlace" class="form-control"
                   value="<?= htmlspecialchars($url_enlace); ?>">
        </div>

        <div class="mb-3">
            <label for="activo">Estado</label>
            <select id="activo" name="activo" class="form-select">
                <option value="1" <?= $activo == 1 ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= $activo == 0 ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <button class="btn btn-success" type="submit">Guardar Cambios</button>
        <a href="/modules/resources/listado_recursos.php" class="btn btn-success">Regresar</a>

    </form>
</div>

<?php include('../../includes/footer.php'); ?>
