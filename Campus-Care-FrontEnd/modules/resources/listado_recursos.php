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

<link rel="stylesheet" href="../../assets/css/listado_recursos.css">

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

<script src="../../assets/js/listado_recursos.js"></script>

<?php include ('../../includes/footer.php'); ?>
