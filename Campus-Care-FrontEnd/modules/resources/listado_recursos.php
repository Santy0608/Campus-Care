<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/listado_recursos.css">

<div class="contenedor-lista">

    <h1>Listado de Recursos</h1>

    <div id="recursosMensaje" class="alert d-none mt-3" role="alert"></div>

    <div class="top-actions d-flex justify-content-between">

        <a href="/modules/resources/agregar_recurso.php" class="btn btn-success">
            Agregar Recurso
        </a>

        <form class="d-flex search-box" id="formBusqueda">
            <input class="form-control me-2"
                name="buscar"
                id="campoBuscar"
                type="search"
                placeholder="Buscar recurso..."
            >
            <button class="btn btn-outline-primary" id="buscarBoton" type="submit">
                Buscar
            </button>
        </form>

    </div>
    <div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>URL</th>
                <th>Fecha</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody id="recursosBody">
            <tr>
                <td colspan="8" class="text-center text-muted">
                    Cargando recursos...
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../../assets/js/listado_recursos.js"></script>

<?php include '../../includes/footer.php'; ?>
