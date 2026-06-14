<?php
include '../../includes/auth.php';
validarAcceso('admin');
require '../../includes/db.php';
include('../../includes/header.php');

try {
    $stmt = $pdo->query('SELECT * FROM usuarios ORDER BY id_usuario ASC');
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener usuarios " . $e->getMessage();
}
?>

<link rel="stylesheet" href="../../assets/css/listado_estudiantes.css">

<div class="usuarios-card">

    <div class="text-center">
        <h1>Listado de Usuarios</h1>
    </div>

    <div class="container search-box">
        <form class="d-flex" role="search" id="formBusqueda" method="GET">
            <input class="form-control me-2" name="buscar" type="search" placeholder="Buscar usuario..."
                aria-label="Buscar" id="campoBuscar">
            <button class="btn btn-outline-primary me-2" id="buscarBoton" type="submit">Buscar</button>
        </form>
    </div>

    <?php
        $search = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

        if (strlen($search) > 3) {
            $consulta = "SELECT * FROM usuarios 
                         WHERE CONCAT(nombre, ' ', apellido, ' ', email) LIKE :buscar";
            $sentencia = $pdo->prepare($consulta);
            $sentencia->bindValue(':buscar', '%' . $search . '%', PDO::PARAM_STR);
            $sentencia->execute();
        } else {
            $consulta = "SELECT * FROM usuarios";
            $sentencia = $pdo->query($consulta);
        }

        $usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="table-wrap my-4">
        <div class="table-responsive">
            <table class="table align-middle text-center">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($usuarios): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                        <td><?= htmlspecialchars($usuario['role']) ?></td>
                        <td>
                            <a href="editar_estudiantes.php?id_usuario=<?= $usuario['id_usuario'] ?>"
                                class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted">No hay registros en el sistema de Usuarios</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="../../assets/js/listado_estudiantes.js"></script>

<?php include('../../includes/footer.php'); 
?>