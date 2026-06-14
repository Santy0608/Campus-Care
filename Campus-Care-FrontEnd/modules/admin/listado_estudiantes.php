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

<style>
    body {
        background-image: url('../../assets/images/listadoEstudiante.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: 'Segoe UI', sans-serif;
    }

    .usuarios-card {
        background: rgba(255, 255, 255, 0.60);
        backdrop-filter: blur(12px);
        padding: 35px;
        border-radius: 18px;
        max-width: 1400px;
        margin: auto;
        margin-top: 60px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.20);
    }

    h1 {
        font-weight: 700;
        color: #1f2d3d;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.9);
        margin-bottom: 25px;
    }

    .search-box {
        max-width: 500px;
        margin: auto;
        margin-top: 25px;
    }

    .table-wrap {
        max-width: 1300px;
        margin: 0 auto;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: rgba(255,255,255,0.95);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0,0,0,0.15);
    }

    thead {
        background: #003366;
        color: white;
        font-size: 1.05rem;
    }

    thead th {
        padding: 14px;
        letter-spacing: 0.5px;
    }

    thead th:first-child { border-top-left-radius: 12px; }
    thead th:last-child { border-top-right-radius: 12px; }

    tbody tr {
        transition: all 0.18s ease;
    }

    tbody tr:hover {
        background: rgba(52, 152, 219, 0.06);
    }

    tbody td {
        padding: 14px;
        font-size: 0.95rem;
        color: #2d2d2d;
    }

    .btn-warning {
        border-radius: 8px;
        padding: 5px 15px;
    }

    @media (max-width: 768px) {
        .usuarios-card { padding: 20px; margin-top: 30px; }
        thead th, tbody td { padding: 10px; font-size: 0.9rem; }
    }
</style>

<div class="usuarios-card">

    <div class="text-center">
        <h1>Listado de Usuarios</h1>
    </div>

    <div class="container search-box">
        <form class="d-flex" role="search" id="formBusqueda" method="GET">
            <input class="form-control me-2" name="buscar" type="search" placeholder="Buscar usuario..." aria-label="Buscar" id="campoBuscar">
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
                                    <a href="editar_estudiantes.php?id_usuario=<?= $usuario['id_usuario'] ?>" class="btn btn-warning btn-sm">Editar</a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const boton = document.getElementById("buscarBoton");
    const campoBuscar = document.getElementById("campoBuscar");

    const urlParams = new URLSearchParams(window.location.search);
    const valorBusqueda = urlParams.get('buscar');

    if (valorBusqueda && valorBusqueda.trim().length > 0) {
        boton.textContent = "Reestablecer";
    }

    boton.addEventListener('click', function(e) {
        if (boton.textContent.trim() === "Reestablecer") {
            e.preventDefault();
            campoBuscar.value = "";
            window.location.href = window.location.pathname;
        }
    });
});
</script>

<?php include('../../includes/footer.php'); 
?>
