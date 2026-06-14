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

<style>
body {
    background: #f0f2f5;
    font-family: 'Segoe UI', sans-serif;
}

.contenedor-listado {
    background: rgba(255,255,255,0.85);
    padding: 35px;
    border-radius: 18px;
    max-width: 1400px;
    margin: 60px auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.contenedor-listado:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.25);
}

h1 {
    font-weight: 700;
    color: #1f2d3d;
    margin-bottom: 25px;
}

.search-box {
    max-width: 500px;
    margin: 25px auto;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
}

thead {
    background: #003366;
    color: white;
    font-size: 1.07rem;
}

thead th {
    padding: 14px;
    font-weight: 600;
}

tbody tr {
    transition: background 0.18s ease;
    border-bottom: 1px solid #dcdcdc;
}

tbody tr:hover {
    background: rgba(0,51,102,0.08);
}

tbody td {
    padding: 14px;
    font-size: 0.96rem;
    color: #2d2d2d;
}

.btn-warning, .btn-danger, .btn-success {
    border-radius: 8px;
    padding: 6px 16px;
}

.top-actions {
    max-width: 1300px;
    margin: 0 auto 20px auto;
}

@media (max-width: 768px) {
    .contenedor-listado { padding: 20px; margin-top: 30px; }
    thead th, tbody td { padding: 10px; font-size: 0.9rem; }
}
</style>

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
                    window.location.href = 'eliminar_frase.php?id_frase=' + id;
                }
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const boton = document.getElementById("buscarBoton");
    const campoBuscar = document.getElementById("campoBuscar");

    const params = new URLSearchParams(window.location.search);
    const valor = params.get('buscar');

    if (valor && valor.trim().length > 0) {
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

<?php include('../../includes/footer.php'); ?>
