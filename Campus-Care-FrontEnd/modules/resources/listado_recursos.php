<?php

include '../../includes/auth.php';
validarAcceso('admin');
require '../../includes/db.php';
include('../../includes/header.php');

try {
    $stmt = $pdo->query('SELECT * FROM recursos ORDER BY id_recurso ASC');
    $recursos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener recursos " . $e->getMessage();
}

$search = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

if (strlen($search) > 3){
    $consulta = "SELECT * FROM recursos 
                 WHERE CONCAT(titulo, ' ', categoria, ' ', contenido) LIKE :buscar";
    $sentencia = $pdo->prepare($consulta);
    $sentencia->bindValue(':buscar', '%' . $search . '%', PDO::PARAM_STR);
    $sentencia->execute();
} else {
    $consulta = "SELECT * FROM recursos";
    $sentencia = $pdo->query($consulta);
}

$recursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
body {
    background-image: url('../../assets/images/recursos.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Segoe UI', sans-serif;
}

.contenedor-lista {
    background: rgba(255, 255, 255, 0.60);
    backdrop-filter: blur(12px);
    padding: 35px;
    border-radius: 18px;
    max-width: 1400px;
    margin: 60px auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.20);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.contenedor-lista:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(0,0,0,0.30);
}

h1 {
    font-weight: 700;
    font-size: 32px;
    color: #1f2d3d;
    text-align: center;
    margin-bottom: 25px;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.9);
}

.top-actions {
    max-width: 1300px;
    margin: auto;
    margin-bottom: 22px;
}

.search-box input {
    border-radius: 12px;
    padding: 12px;
    font-size: 15px;
    border: 1px solid #c7c7c7;
    transition: all .15s ease;
}

.search-box input:focus {
    border-color: #003366;
    box-shadow: 0 0 6px rgba(0,51,102,0.3);
}

table {
    width: 100%;
    background: white;
    border-radius: 14px;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
    font-size: 1rem;
}

thead {
    background: #003366;
    color: white;
}

thead th {
    padding: 14px;
    font-weight: 600;
    text-align: center;
}

tbody tr {
    transition: all 0.20s ease;
    border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
    background: rgba(0, 51, 102, 0.08);
    transform: scale(1.002);
}

tbody td {
    padding: 14px;
    color: #1f2d3d;
    font-size: 0.97rem;
    text-align: center;
}

.btn-success, .btn-warning, .btn-danger, .btn-outline-primary {
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 15px;
    font-weight: 600;
}
</style>

<div class="contenedor-lista">

    <h1>Listado de Recursos</h1>

    <div class="top-actions d-flex justify-content-between">

        <a href="/modules/resources/agregar_recurso.php" class="btn btn-success">
            Agregar Recurso
        </a>

        <form class="d-flex search-box" method="GET">
            <input class="form-control me-2"
                name="buscar"
                type="search"
                placeholder="Buscar recurso..."
                value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit">
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
                <th>Contenido</th>
                <th>URL</th>
                <th>Categoría</th>
                <th>Fecha</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($recursos): ?>
            <?php foreach ($recursos as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['id_recurso']) ?></td>
                    <td><?= htmlspecialchars($r['titulo']) ?></td>
                    <td><?= htmlspecialchars($r['tipo']) ?></td>
                    <td><?= htmlspecialchars($r['contenido']) ?></td>
                    <td><?= htmlspecialchars($r['url_enlace']) ?></td>
                    <td><?= htmlspecialchars($r['categoria']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_publicacion']) ?></td>
                    <td><?= $r['activo'] == 1 ? 'Sí' : 'No' ?></td>

                    <td>
                        <a href="actualizar_recurso.php?id_recurso=<?= $r['id_recurso'] ?>"
                           class="btn btn-warning btn-sm">
                            Actualizar
                        </a>

                        <button class="btn btn-danger btn-sm btn-eliminar"
                                data-id="<?= $r['id_recurso'] ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No hay recursos registrados
                    </td>
                </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botones = document.querySelectorAll('.btn-eliminar');

    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.dataset.id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'eliminar_recurso.php?id_recurso=' + id;
                }
            });
        });
    });
});
</script>

<?php include ('../../includes/footer.php'); ?>
