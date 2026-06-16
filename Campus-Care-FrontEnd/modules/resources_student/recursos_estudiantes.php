<?php
include '../../includes/auth.php';
validarAcceso('estudiante');
require_once(__DIR__ . '/../../includes/db.php');
include('../../includes/header.php');

// Función para formatear nombres de categorías
function formatearCategoria($categoria) {
    $nombres = [
        'estres' => 'Estrés',
        'ansiedad' => 'Ansiedad',
        'sueno' => 'Sueño',
        'estado_animo' => 'Estado De Ánimo',
        'relaciones' => 'Relaciones',
        'motivacion' => 'Motivación'
    ];
    
    return $nombres[$categoria] ?? ucwords(str_replace('_', ' ', $categoria));
}

// Iconos por categoría
$iconosCategorias = [
    'estres' => '😫',
    'ansiedad' => '😰',
    'sueno' => '😴',
    'estado_animo' => '🙂',
    'relaciones' => '💬',
    'motivacion' => '🔥'
];

$search = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";
$filtroCategoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";

$categoriasStmt = $pdo->query("SELECT DISTINCT categoria FROM recursos ORDER BY categoria ASC");
$categorias = $categoriasStmt->fetchAll(PDO::FETCH_COLUMN);

$where = [];
$params = [];

if (strlen($search) > 3) {
    $where[] = "CONCAT(titulo, ' ', tipo, ' ', contenido) LIKE :buscar";
    $params[':buscar'] = "%" . $search . "%";
}

if (!empty($filtroCategoria) && $filtroCategoria !== "todas") {
    $where[] = "categoria = :categoria";
    $params[':categoria'] = $filtroCategoria;
}

$sql = "SELECT * FROM recursos";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY categoria, id_recurso ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recursosPorCategoria = [];
foreach ($recursos as $r) {
    $cat = $r['categoria'] ?? 'Sin categoría';
    $recursosPorCategoria[$cat][] = $r;
}
?>

<br><br>

<div class="text-center">
    <h1><span class="corazon-azul">💙</span> Recursos</h1>
</div>

<br>

<div class="container my-4">
    <form class="row g-3" method="GET">

        <div class="col-md-4">
            <select class="form-select" name="categoria" onchange="this.form.submit()">
                <option value="todas">Todas las categorías</option>

                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= ($filtroCategoria === $cat) ? "selected" : "" ?>>
                        <?= formatearCategoria($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <input class="form-control" name="buscar" type="search" placeholder="Buscar recurso..." 
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100" type="submit">Buscar</button>
        </div>

    </form>
</div>

<div class="container mt-4">

<?php if (!empty($recursos)): ?>

    <?php foreach ($recursosPorCategoria as $categoria => $items): ?>        
       <h2 class="mt-5 mb-3 border-bottom pb-2">
            <?= formatearCategoria($categoria) ?>
        </h2>

        <div class="row g-4">
            <?php foreach ($items as $r): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-3 card-hover">
                        <div class="card-body p-4">

                            <h5 class="card-title fw-bold d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($r['titulo']) ?>
                                <span class="icono-categoria">
                                    <?= $iconosCategorias[$r['categoria']] ?? '💤' ?>
                                </span>
                            </h5>

                            <h6 class="card-subtitle mb-2 text-muted">
                                Tipo: <?= htmlspecialchars($r['tipo']) ?>
                            </h6>

                            <p class="card-text mt-2">
                                <?= htmlspecialchars(substr($r['contenido'], 0, 120)) ?>
                                <?= strlen($r['contenido']) > 120 ? "..." : "" ?>
                            </p>

                        </div>

                        <div class="card-footer bg-transparent border-0 px-4 pb-4">
                            <a href="<?= htmlspecialchars($r['url_enlace']) ?>" target="_blank" 
                               class="btn btn-primary w-100 rounded-pill">
                                Ver más
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="alert alert-warning text-center">No hay recursos disponibles.</div>

<?php endif; ?>

</div>

<style>
.card-hover {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

.icono-categoria {
    font-size: 1.5rem;
    margin-left: 10px;
}

.corazon-azul {
    margin-right: 8px;
}
</style>

<?php include('../../includes/footer.php'); ?>
