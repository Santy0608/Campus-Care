<?php

require_once(__DIR__ . '/../../includes/db.php');
include '../../includes/auth.php';
validarAcceso('estudiante');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])){
    header('Location: ../../login/indexLogin.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario']; 

if (!isset($pdo)) {
    die("Error: No se pudo conectar a la base de datos.");
}

//Lógica de gamificación en dashboard.php
$racha_actual = 0;
$puntos_totales = 0;
try {
    $sql_gam = 'SELECT racha_actual, puntos_totales FROM usuarios WHERE id_usuario = :id_usuario';
    $stmt_gam = $pdo->prepare($sql_gam);
    $stmt_gam->execute(['id_usuario' => $id_usuario]);
    $gam_data = $stmt_gam->fetch(PDO::FETCH_ASSOC);
    
    if ($gam_data) {
        $racha_actual = $gam_data['racha_actual'];
        $puntos_totales = $gam_data['puntos_totales'];
    }
} catch (PDOException $e) {
    // Manejar error de carga de gamificación
}




$evaluaciones_data = [];
$labels = [];
$datasets = [
    'estres' => ['label' => 'Estrés 😣', 'data' => [], 'color' => 'rgb(255, 99, 132)'],
    'ansiedad' => ['label' => 'Ansiedad 😟', 'data' => [], 'color' => 'rgb(255, 159, 64)'],
    'sueno' => ['label' => 'Sueño 😴', 'data' => [], 'color' => 'rgb(54, 162, 235)'],
    'estado_animo' => ['label' => 'Ánimo 😊', 'data' => [], 'color' => 'rgb(75, 192, 192)'],
    'relaciones' => ['label' => 'Relaciones 💔', 'data' => [], 'color' => 'rgb(153, 102, 255)'],
    'motivacion' => ['label' => 'Motivación 💪', 'data' => [], 'color' => 'rgb(201, 203, 207)'],
];
$recursos_recomendados = [];
$categoria_foco = null;

try {
    $sql_evaluaciones = 'SELECT * FROM autoevaluaciones 
                         WHERE id_usuario = :id_usuario 
                         ORDER BY fecha_evaluacion DESC LIMIT 7';
    $stmt_evaluaciones = $pdo->prepare($sql_evaluaciones);
    $stmt_evaluaciones->execute(['id_usuario' => $id_usuario]);
    $evaluaciones_data = $stmt_evaluaciones->fetchAll(PDO::FETCH_ASSOC);

    $evaluaciones_data = array_reverse($evaluaciones_data); 

    foreach ($evaluaciones_data as $evaluacion) {
        $labels[] = date('d/M', strtotime($evaluacion['fecha_evaluacion']));
        
        $datasets['estres']['data'][] = $evaluacion['estres_score'];
        $datasets['ansiedad']['data'][] = $evaluacion['ansiedad_score'];
        $datasets['sueno']['data'][] = $evaluacion['sueno_score'];
        $datasets['estado_animo']['data'][] = $evaluacion['estado_animo_score'];
        $datasets['relaciones']['data'][] = $evaluacion['relaciones_score'];
        $datasets['motivacion']['data'][] = $evaluacion['motivacion_score'];
    }

    if (!empty($evaluaciones_data)) {
        $ultima_evaluacion = end($evaluaciones_data); 
        $min_score = 6; 
        $categoria_foco = 'general'; 

        foreach ($datasets as $key => $value) {
            $score = $ultima_evaluacion[$key . '_score'];
            if ($score < $min_score) {
                $min_score = $score;
                $categoria_foco = $key;
            }
        }
    }
} catch (PDOException $e) {
    echo "Error al cargar evaluaciones: " . $e->getMessage();
}

if ($categoria_foco && $categoria_foco !== 'general') {
    try {
        $sql_recursos = 'SELECT * FROM recursos 
                         WHERE categoria = :categoria_foco AND activo = 1 
                         ORDER BY id_recurso DESC LIMIT 3';
        $stmt_recursos = $pdo->prepare($sql_recursos);
        $stmt_recursos->execute(['categoria_foco' => $categoria_foco]);
        $recursos_recomendados = $stmt_recursos->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al cargar recursos: " . $e->getMessage();
    }
}

$chart_datasets = [];
foreach ($datasets as $data) {
    if (!empty($data['data'])) {
        $chart_datasets[] = [
            'label' => $data['label'],
            'data' => $data['data'],
            'borderColor' => $data['color'],
            'backgroundColor' => $data['color'] . '40', 
            'fill' => false,
            'tension' => 0.4,
        ];
    }
}
$chart_datasets_json = json_encode($chart_datasets);
$labels_json = json_encode($labels);

$mensaje_exito = null;
if (isset($_GET['msg'])) {
    $mensaje_exito = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';   
}

// Incluir el header después de toda la lógica de procesamiento
include ('../../includes/header.php');

?>




<br><br><br>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Mi Progreso Emocional
                    </h2>
                    <p class="mb-0 opacity-75">Visualiza tu evolución en las diferentes áreas de bienestar</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($evaluaciones_data)): ?>


                    <!-- Gamificación -->
                        <div class="container my-5">
    
                            <div class="card shadow-lg p-4 gamification-card"> <h2 class="display-5 fw-bolder text-center text-white mb-4">
                                    <span class="d-block d-md-inline">🏆 Resumen de Logros 🚀</span>
                                </h2>

                                <div class="d-flex justify-content-center align-items-stretch gap-5 flex-wrap">
            
                                    <div class="gamification-stat-card focus-card p-4 border-warning">
                                        <div class="icon-container mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#FFD700" viewBox="0 0 24 24">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        </div>
                                        <p class="small text-uppercase mb-1 fw-bold">Puntos Totales</p>
                                        <p class="h1 fw-bolder text-warning mb-0">
                                            <?php echo number_format($puntos_totales); ?>
                                        </p>
                                    </div>

                                    <div class="gamification-stat-card focus-card p-4 border-danger">
                                        <div class="icon-container mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#FF4136" viewBox="0 0 24 24">
                                                <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6c-1.89 0-3.59-.88-4.71-2.26L5.8 17.5c1.47 1.76 3.65 2.89 6.2 2.89 4.42 0 8-3.58 8-8s-3.58-8-8-8z"/>
                                            </svg>
                                        </div>
                                        <p class="small text-uppercase mb-1 fw-bold">Días de Racha 🔥</p>
                                        <p class="h1 fw-bolder text-danger mb-0">
                                            <?php echo $racha_actual; ?>
                                        </p>
                                    </div>

                                </div>
                                
                            </div>

                        </div>


                        <!-- Gráfico de líneas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-chart-area me-2"></i>Evolución de tus evaluaciones
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="progressChart" style="height: 500px; width: 800px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas resumidas -->
                        <div class="row mb-4">
                            <?php 
                            $ultima_evaluacion = end($evaluaciones_data);
                            $categorias = [
                                'estres' => ['label' => 'Estrés', 'icon' => '😣', 'color' => 'danger'],
                                'ansiedad' => ['label' => 'Ansiedad', 'icon' => '😟', 'color' => 'warning'],
                                'sueno' => ['label' => 'Sueño', 'icon' => '😴', 'color' => 'info'],
                                'estado_animo' => ['label' => 'Ánimo', 'icon' => '😊', 'color' => 'success'],
                                'relaciones' => ['label' => 'Relaciones', 'icon' => '💔', 'color' => 'secondary'],
                                'motivacion' => ['label' => 'Motivación', 'icon' => '💪', 'color' => 'primary']
                            ];
                            foreach ($categorias as $key => $cat): 
                                $score = $ultima_evaluacion[$key . '_score'];
                                $nivel = $score <= 3 ? 'Bajo' : ($score <= 6 ? 'Moderado' : 'Alto');
                            ?>
                            <div class="col-md-4 col-lg-2 mb-3">
                                <div class="card text-center h-100 border-<?php echo $cat['color']; ?>">
                                    <div class="card-body p-3">
                                        <div class="display-6 mb-2"><?php echo $cat['icon']; ?></div>
                                        <h6 class="card-title mb-1"><?php echo $cat['label']; ?></h6>
                                        <div class="h4 text-<?php echo $cat['color']; ?> mb-1"><?php echo $score; ?>/10</div>
                                        <small class="text-muted"><?php echo $nivel; ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Recursos recomendados -->
                        <?php if (!empty($recursos_recomendados)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-lightbulb me-2 text-warning"></i>Recursos Recomendados para Ti
                                        </h5>
                                        <small class="text-muted">Basado en tu última evaluación</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($recursos_recomendados as $recurso): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100 border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="fas fa-seedling me-1 text-success"></i>
                                                            <?php echo htmlspecialchars($recurso['titulo'] ?? 'Sin título'); ?>
                                                        </h6>
                                                        <p class="card-text small">
                                                            <?php 
                                                            $contenido = $recurso['contenido'] ?? '';
                                                            echo $contenido ? htmlspecialchars(substr($contenido, 0, 100)) . '...' : 'Sin descripción disponible';
                                                            ?>
                                                        </p>
                                                        <?php if (!empty($recurso['url_enlace'])): ?>
                                                            <a href="<?php echo htmlspecialchars($recurso['url_enlace']); ?>" 
                                                               class="btn btn-outline-success btn-sm" target="_blank">
                                                                <i class="fas fa-external-link-alt me-1"></i>Ver recurso
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted small">
                                                                <i class="fas fa-info-circle me-1"></i>Recurso interno
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Estado vacío -->
                        <div class="text-center py-5">
                            <div class="display-1 text-muted mb-3">📊</div>
                            <h4 class="text-muted mb-3">¡Aún no tienes evaluaciones!</h4>
                            <p class="text-muted mb-4">Realiza tu primera autoevaluación para comenzar a ver tu progreso emocional.</p>
                            <a href="<?php echo url('modules/autoevaluacion/autoevaluacion.php'); ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-heart me-2"></i>Realizar Autoevaluación
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php 
if ($mensaje_exito): 
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mensaje = <?php echo json_encode($mensaje_exito); ?>; 
    if (mensaje && mensaje.length > 0) {
        const modalBody = document.getElementById('modal-felicitacion-body');
        modalBody.innerHTML = mensaje;
        const felicitacionModal = new bootstrap.Modal(document.getElementById('modal-felicitacion'));
        felicitacionModal.show();
    }
});
</script>
<?php 
endif; 
?>


<!-- Modal de Gamificación -->
<div class="modal fade" id="modal-felicitacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content modal-gamificacion"> <div class="modal-background-effect"></div> 
      
      <div class="modal-header border-0 pb-0 d-block text-center position-relative"> 
        <h2 class="modal-title display-5 fw-bold text-success mb-2" id="modalLabel">
          <span class="animated-icon">🏆</span> ¡Nivel Subido!
        </h2>
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body text-center pt-0" id="modal-felicitacion-body">
        <p class="lead">¡Has completado la misión y ganado **150 puntos**!</p>
        <div class="my-4">
             
        </div>
        <p class="h4">¡Sigue así para desbloquear la siguiente insignia!</p>
      </div>
      
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-success btn-lg shadow-sm px-5" data-bs-dismiss="modal">
          ¡A por más!
        </button>
      </div>
      
    </div>
  </div>
</div>

</body>

<!-- Scripts para el gráfico -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($evaluaciones_data)): ?>
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $labels_json; ?>,
            datasets: <?php echo $chart_datasets_json; ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Puntuación (1-10)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fechas de evaluación'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    <?php endif; ?>
});
</script>




<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.border-danger { border-color: #dc3545 !important; }
.border-warning { border-color: #ffc107 !important; }
.border-info { border-color: #17a2b8 !important; }
.border-success { border-color: #28a745 !important; }
.border-secondary { border-color: #6c757d !important; }
.border-primary { border-color: #007bff !important; }



/* Estilos para el apartado de modal de gamificacion */


.modal-gamificacion {
    border-radius: 20px; 
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); 
    background: linear-gradient(145deg, #ffffff, #f0f0f0); 
    overflow: hidden; 
}

.modal-gamificacion .modal-background-effect {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: #FFD700; 
    opacity: 0.1;
    clip-path: polygon(0 0, 100% 0, 100% 70%, 0 100%); 
    z-index: 0; 
}

.modal-gamificacion .modal-content {
    position: relative;
    z-index: 10; 
}

.modal-gamificacion .modal-title {
    color: #4CAF50 !important; 
    font-size: 2.5rem; 
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.modal-gamificacion .btn-close {
    opacity: 0.8;
    background-color: white;
    border-radius: 50%;
}

.modal-gamificacion .btn-success {
    background-color: #FF6F61; 
    border-color: #FF6F61;
    transition: transform 0.2s;
}

.modal-gamificacion .btn-success:hover {
    transform: scale(1.05); 
    background-color: #e55c50;
    border-color: #e55c50;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
    40% {transform: translateY(-10px);}
    60% {transform: translateY(-5px);}
}

.animated-icon {
    display: inline-block;
    animation: bounce 1.5s ease-in-out infinite; 
}







.gamification-card {
    background: #1e1e2d; 
    border-radius: 20px;
    border: 3px solid #36454F; 
    position: relative;
    overflow: hidden;
    text-align: center;
    color: white; 
}

.gamification-stat-card {
    background-color: #2a2a3f; 
    border-radius: 15px; 
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5); 
    min-width: 250px; 
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid rgba(255, 255, 255, 0.1); 
    
    &:hover {
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6); 
    }
}

.icon-container {
    background: rgba(255, 255, 255, 0.05); 
    border-radius: 50%;
    padding: 10px;
    display: inline-flex;
    margin-bottom: 10px;
}

.focus-card .h1 {
    font-size: 4rem; 
    line-height: 1;
}

.text-warning { color: #FFD700 !important; }
.text-danger { color: #FF4136 !important; } 
.text-white { color: #f8f9fa !important; }



</style>


<?php

    include ('../../includes/footer.php');

?>