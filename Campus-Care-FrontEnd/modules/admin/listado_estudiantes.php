<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/listado_estudiantes.css">

<div class="usuarios-card">

    <div class="text-center">
        <h1>Listado de Usuarios</h1>
    </div>

    <div id="usuariosMensaje" class="alert d-none mt-3" role="alert"></div>

    <div class="container search-box">
        <form class="d-flex" role="search" id="formBusqueda">
            <input class="form-control me-2" name="buscar" type="search" placeholder="Buscar usuario..."
                aria-label="Buscar" id="campoBuscar">
            <button class="btn btn-outline-primary me-2" id="buscarBoton" type="submit">Buscar</button>
        </form>
    </div>

    <div class="table-wrap my-4">
        <div class="table-responsive">
            <table class="table align-middle text-center">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuariosBody">
                    <tr>
                        <td colspan="6" class="text-muted">Cargando usuarios...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="../../assets/js/listado_estudiantes.js"></script>

<?php include '../../includes/footer.php'; ?>