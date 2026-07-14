<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/agregar_recurso.css">

<div class="form-wrapper">

    <h2>Agregar Recurso</h2>

    <div id="recursoMensaje" class="alert d-none" role="alert"></div>

    <form id="agregarRecursoForm">

        <label for="titulo">Título del Recurso:</label>
        <input type="text" name="titulo" id="titulo" class="form-control" required>

        <label for="tipoRecursoId">Tipo de Recurso:</label>
        <select name="tipoRecursoId" class="form-select" id="tipoRecursoId" required>
            <option value="">Cargando tipos de recurso...</option>
        </select>

        <label for="categoriaId">Categoría:</label>
        <select name="categoriaId" class="form-select" id="categoriaId" required>
            <option value="">Cargando categorias...</option>
        </select>

        <label for="contenido">Contenido / Instrucciones:</label>
        <textarea name="contenido" id="contenido" class="form-control" rows="7" required></textarea>

        <label for="url_enlace">URL o Enlace:</label>
        <input type="url" name="urlEnlace" class="form-control" id="url_enlace" required>

        <label for="activo">Estado:</label>
        <select name="activo" class="form-select" id="activo">
            <option value="true">Activo (Publicado)</option>
            <option value="false">Inactivo (Borrador)</option>
        </select>

        <div class="mt-4">
            <button class="btn btn-success" type="submit">Agregar Recurso</button>
            <a href="/modules/resources/listado_recursos.php" class="btn btn-success">Regresar</a>
        </div>

    </form>
</div>

<script src="../../assets/js/agregar_recurso.js"></script>

<?php include '../../includes/footer.php'; ?>
