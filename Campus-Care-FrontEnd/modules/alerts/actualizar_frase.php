<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/actualizar_frase.css">

<div class="form-wrapper">
    <h2>Actualizar Frase</h2>

    <div id="fraseMensaje" class="alert d-none" role="alert"></div>

    <form id="actualizarFraseForm">

        <div class="mb-3">
            <label for="texto" class="form-label">Texto</label>
            <input 
                type="text" 
                id="texto" 
                name="texto" 
                class="form-control" 
                required
            >
        </div>

        <div class="mb-3">
            <label for="autor" class="form-label">Autor</label>
            <input
                type="text"
                id="autor"
                name="autor"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label for="activo" class="form-label">Estado</label>
            <select name="activo" class="form-select" id="activo">
                <option value="true">Activo (Publicado)</option>
                <option value="false">
                    Inactivo (Borrador)
                </option>
            </select>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="/modules/alerts/listado_frases.php" class="btn btn-success">Regresar</a>
        </div>
    </form>
</div>

<script src="../../assets/js/actualizar_frase.js"></script>

<?php include '../../includes/footer.php'; ?>
