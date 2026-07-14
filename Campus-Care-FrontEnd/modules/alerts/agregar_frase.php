<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/agregar_frase.css">

<div class="form-wrapper">
    <h2>Agregar Frase</h2>

    <div id="fraseMensaje" class="alert d-none" role="alert"></div>

    <form id="agregarFraseForm">
        
        <div class="mb-3">
            <label for="texto" class="form-label">Texto</label>
            <input 
                type="text" 
                class="form-control" 
                name="texto" 
                id="texto"
                required
            >
        </div>

        <div class="mb-3">
            <label for="autor" class="form-label">Autor</label>
            <input
                type="text"
                class="form-control"
                name="autor"
                id="autor"
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
            <button class="btn btn-success" type="submit">Agregar Frase</button>
            <a href="/modules/alerts/listado_frases.php" class="btn btn-success">Regresar</a>
        </div>
    </form>
</div>

<script src="../../assets/js/agregar_frase.js"></script>

<?php include '../../includes/footer.php'; ?>
