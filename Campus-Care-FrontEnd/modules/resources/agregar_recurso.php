<?php

include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');
include('../../includes/header.php');


$titulo = $tipo = $contenido = $url_enlace = $categoria = $activo = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['titulo'])) {
        $errores['titulo'] = 'El título es obligatorio.';
    } else {
        $titulo = trim($_POST['titulo']);
    }

    if (empty($_POST['tipo'])) {
        $errores['tipo'] = 'El tipo de recurso es obligatorio.';
    } else {
        $tipo = trim($_POST['tipo']);
    }

    if (empty($_POST['contenido'])) {
        $errores['contenido'] = 'El contenido es obligatorio.';
    } else {
        $contenido = trim($_POST['contenido']);
    }

    if (empty($_POST['url_enlace'])) {
        $errores['url_enlace'] = 'La URL es obligatoria.';
    } else {
        $url_enlace = trim($_POST['url_enlace']);
    }

    if (empty($_POST['categoria'])) {
        $errores['categoria'] = 'La categoría es obligatoria.';
    } else {
        $categoria = trim($_POST['categoria']);
    }

    $fecha_publicacion = date('Y-m-d H:i:s');
    $activo = trim($_POST['activo']);

    if (empty($errores)) {
        try {
            $sql = 'INSERT INTO recursos (titulo, tipo, contenido, url_enlace, categoria, fecha_publicacion, activo)
                    VALUES (:titulo, :tipo, :contenido, :url_enlace, :categoria, :fecha_publicacion, :activo)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titulo' => $titulo,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'url_enlace' => $url_enlace,
                'categoria' => $categoria,
                'fecha_publicacion' => $fecha_publicacion,
                'activo' => $activo
            ]);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Recurso agregado',
                    text: 'El recurso se ha agregado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = '/modules/resources/listado_recursos.php';
                });
            </script>";
            exit;

        } catch (PDOException $e) {
            echo "Error al insertar el recurso: " . $e->getMessage();
        }
    }
}
?>

<link rel="stylesheet" href="../../assets/css/agregar_recurso.css">

<div class="form-wrapper">

    <h2>Agregar Recurso</h2>

    <form method="POST" action="agregar_recurso.php">

        <label for="titulo">Título del Recurso:</label>
        <input type="text" name="titulo" id="titulo" class="form-control"
            value="<?php echo htmlspecialchars($titulo); ?>">
        <?php if (isset($errores['titulo'])): ?>
            <p class="error"><?php echo $errores['titulo']; ?></p>
        <?php endif; ?>

        <label for="tipo">Tipo de Recurso:</label>
        <select name="tipo" class="form-select" id="tipo">
            <option value="">-- Seleccione el Tipo --</option>
            <option value="articulo" <?= $tipo === 'articulo' ? 'selected' : '' ?>>Artículo de Texto</option>
            <option value="video" <?= $tipo === 'video' ? 'selected' : '' ?>>Video Embebido</option>
            <option value="ejercicio" <?= $tipo === 'ejercicio' ? 'selected' : '' ?>>Ejercicio Interactivo</option>
        </select>

        <label for="categoria">Categoría:</label>
        <select name="categoria" class="form-select" id="categoria">
            <option value="">-- Seleccione la Categoría --</option>
            <option value="estres" <?= $categoria === 'estres' ? 'selected' : '' ?>>Estrés</option>
            <option value="ansiedad" <?= $categoria === 'ansiedad' ? 'selected' : '' ?>>Ansiedad</option>
            <option value="sueno" <?= $categoria === 'sueno' ? 'selected' : '' ?>>Sueño</option>
            <option value="estado_animo" <?= $categoria === 'estado_animo' ? 'selected' : '' ?>>Estado de Ánimo</option>
            <option value="relaciones" <?= $categoria === 'relaciones' ? 'selected' : '' ?>>Relaciones</option>
            <option value="motivacion" <?= $categoria === 'motivacion' ? 'selected' : '' ?>>Motivación</option>
        </select>

        <label for="contenido">Contenido / Instrucciones:</label>
        <textarea name="contenido" id="contenido" class="form-control" rows="7"><?php echo htmlspecialchars($contenido); ?></textarea>

        <label for="url_enlace">URL o Enlace:</label>
        <input type="url" name="url_enlace" class="form-control" id="url_enlace"
            value="<?php echo htmlspecialchars($url_enlace); ?>">

        <label for="activo">Estado:</label>
        <select name="activo" class="form-select" id="activo">
            <option value="1">Activo (Publicado)</option>
            <option value="0" <?= $activo == "0" ? "selected" : "" ?>>Inactivo (Borrador)</option>
        </select>

        <div class="mt-4">
            <button class="btn btn-success" type="submit">Agregar Recurso</button>
            <a href="/modules/resources/listado_recursos.php" class="btn btn-success">Regresar</a>
        </div>

    </form>
</div>

<?php include('../../includes/footer.php'); ?>
