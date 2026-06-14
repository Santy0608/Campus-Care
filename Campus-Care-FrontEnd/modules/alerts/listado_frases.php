<?php
include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');
include('../../includes/header.php');

try {
    $stmt = $pdo->query('SELECT * FROM frases ORDER BY id_frase ASC');
    $frases = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al listar frases " . $e->getMessage();
}
?>

<link rel="stylesheet" href="../../assets/css/listado_frases.css">

<div class="contenedor-listado">

    <div class="text-center">
        <h1>Control de Frases</h1>
    </div>

    <div class="top-actions d-flex justify-content-start">
        <a href="/modules/alerts/agregar_frase.php">
            <button class="btn btn-success">Agregar Frase</button>
        </a>
    </div>

    <div class="search-box">
        <form class="d-flex" role="search" id="formBusqueda" method="GET">
            <input class="form-control me-2" name="buscar" type="search" placeholder="Buscar frase..." aria-label="Buscar" id="campoBuscar">
            <button class="btn btn-outline-primary me-2" id="buscarBoton" type="submit">Buscar</button>
        </form>
    </div>

    <?php
        $search = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

        if (strlen($search) > 3) {
            $consulta = "SELECT * FROM frases WHERE descripcion LIKE :buscar";
            $sentencia = $pdo->prepare($consulta);
            $sentencia->bindValue(':buscar', '%' . $search . '%', PDO::PARAM_STR);
            $sentencia->execute();
        } else {
            $consulta = "SELECT * FROM frases";
            $sentencia = $pdo->query($consulta);
        }

        $frases = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="table-responsive mt-4">
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>Id Frase</th>
                    <th>Descripción</th>
                    <th>Fecha Publicación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($frases): ?>
                    <?php foreach ($frases as $frase): ?>
                        <tr>
                            <td><?= htmlspecialchars($frase['id_frase']); ?></td>
                            <td><?= htmlspecialchars($frase['descripcion']); ?></td>
                            <td><?= htmlspecialchars($frase['fecha_publicacion']); ?></td>
                            <td><?= htmlspecialchars($frase['activo']); ?></td>
                            <td>
                                <a href="actualizar_frase.php?id_frase=<?= $frase['id_frase']; ?>" class="btn btn-warning">Editar</a>
                                <button class="btn btn-danger btn-eliminar" data-id="<?= $frase['id_frase']; ?>">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-muted">No hay frases registradas</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../../assets/js/listado_frases.js"></script>

<?php include('../../includes/footer.php'); ?>
