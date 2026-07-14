<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/actualizar_recurso.css">

<div class="form-wrapper">

    <h2>Actualizar Recurso</h2>

    <div id="recursoMensaje" class="alert d-none" role="alert"></div>

    <form id="actualizarRecursoForm">

        <div class="mb-3">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tipoRecursoId">Tipo de Recurso</label>
            <select id="tipoRecursoId" name="tipoRecursoId" class="form-select" required>
                <option value="">Cargando tipos de recurso...</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="categoriaId">Categoría</label>
            <select id="categoriaId" name="categoriaId" class="form-select" required>
                <option value="">Cargando categorias...</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="contenido">Contenido</label>
            <textarea id="contenido" name="contenido" rows="8"
                class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="url_enlace">URL o Enlace</label>
            <input type="url" id="url_enlace" name="url_enlace" class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label for="activo">Estado</label>
            <select id="activo" name="activo" class="form-select">
                <option value="true">Activo</option>
                <option value="false">Inactivo</option>
            </select>
        </div>

        <button class="btn btn-success" type="submit">Guardar Cambios</button>
        <a href="/modules/resources/listado_recursos.php" class="btn btn-success">Regresar</a>

    </form>
</div>

<script src="../../assets/js/actualizar_recurso.js"></script>

<?php include '../../includes/footer.php'; ?>