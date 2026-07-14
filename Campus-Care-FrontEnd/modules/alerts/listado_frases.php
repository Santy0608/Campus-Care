<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/listado_frases.css">

<div class="contenedor-listado">

    <div class="text-center">
        <h1>Control de Frases</h1>
    </div>

    <div id="frasesMensaje" class="alert d-none mt-3" role="alert"></div>

    <div class="top-actions d-flex justify-content-start">
        <a href="/modules/alerts/agregar_frase.php">
            <button class="btn btn-success">Agregar Frase</button>
        </a>
    </div>

    <div class="search-box">
        <form class="d-flex" role="search" id="formBusqueda">
            <input class="form-control me-2" name="buscar" type="search" placeholder="Buscar frase..." aria-label="Buscar" id="campoBuscar">
            <button class="btn btn-outline-primary me-2" id="buscarBoton" type="submit">Buscar</button>
        </form>
    </div>

    <div class="table-responsive mt-4">
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>Id Frase</th>
                    <th>Texto</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody id="frasesBody">
                <tr><td colspan="5" class="text-muted">Cargando frases...</td></tr>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../../assets/js/listado_frases.js"></script>

<?php include '../../includes/footer.php'; ?>
