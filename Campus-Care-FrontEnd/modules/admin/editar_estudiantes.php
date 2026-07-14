<?php include '../../includes/header.php'; ?>

<link rel="stylesheet" href="../../assets/css/editar_estudiantes.css">

<div class="usuarios-card">

    <div class="text-center">
        <h1>Editar Usuario</h1>
    </div>

    <div id="usuarioMensaje" class="alert d-none" role="alert"></div>

    <form id="editarUsuarioForm">

        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellido:</label>
            <input type="text" name="apellido" id="apellido" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre de usuario:</label>
            <input type="text" name="nombreUsuario" id="nombreUsuario" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nueva contraseña:</label>
            <input type="password" name="contrasenia" id="contrasenia" class="form-control"
                   placeholder="Deje este campo vacio para conservar la actual">
        </div>

        <div class="mb-3">
            <label class="form-label">Rol:</label>
            <select name="role" id="role" class="form-select">
                <option value="estudiante">Estudiante</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="listado_estudiantes.php" class="btn btn-secondary">Volver</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>

</div>

<script src="../../assets/js/editar_estudiantes.js"></script>

<?php include '../../includes/footer.php'; ?>
