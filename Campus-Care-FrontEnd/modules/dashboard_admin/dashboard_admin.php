<?php

include '../../includes/auth.php';
validarAcceso('admin');
require_once(__DIR__ . '/../../includes/db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sql_promedios = "
    SELECT AVG(t1.estres_score) AS avg_estres,
    AVG(t1.ansiedad_score) AS avg_ansiedad,
    AVG(t1.sueno_score) AS avg_sueno,
    AVG(t1.estado_animo_score) AS avg_estado_animo,
    AVG(t1.relaciones_score) AS avg_relaciones,
    AVG(t1.motivacion_score) AS avg_motivacion
    FROM autoevaluaciones t1 WHERE t1.fecha_evaluacion = (
        SELECT MAX(t2.fecha_evaluacion) FROM autoevaluaciones t2 WHERE t2.id_usuario = t1.id_usuario)
";

$datos_promedio = [];
try{
    $stmt = $pdo->query($sql_promedios);
    $datos_promedio = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e){
    error_log('Error al cargar datos del dashboard administrativo: ' . $e->getMessage());
    $datos_promedio = array_fill_keys(['avg_estres', 'avg_ansiedad', 'avg_sueno', 'avg_estado_animo', 'avg_relaciones', 'avg_motivacion'], 0);
}

$categorias = [
    'Estrés', 'Ansiedad', 'Sueño', 'Estado de Ánimo', 'Relaciones', 'Motivacion'
];



$colores_categorias = [
    'rgba(255, 99, 132, 1)', 
    'rgba(255, 159, 64, 1)',  
    'rgba(54, 162, 235, 1)',  
    'rgba(75, 192, 192, 1)',  
    'rgba(153, 102, 255, 1)', 
    'rgba(201, 203, 207, 1)'  
];

$scores_actuales = [
    round($datos_promedio['avg_estres'] ?? 0, 1),
    round($datos_promedio['avg_ansiedad'] ?? 0, 1),
    round($datos_promedio['avg_sueno'] ?? 0, 1),
    round($datos_promedio['avg_estado_animo'] ?? 0, 1),
    round($datos_promedio['avg_relaciones'] ?? 0, 1),
    round($datos_promedio['avg_motivacion'] ?? 0, 1),
];

$chart_data =  [
    'labels' => $categorias,
    'datasets' => [[
        'label' => 'Puntuación Promedio de Bienestar',
        'data' => $scores_actuales,
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'pointBackgroundColor' => $colores_categorias,
        'pointBorderColor' => '#fff',
        'pointHoverBackgroundColor' => '#fff',
        'pointHoverBorderColor' => 'rgba(255, 99, 132, 1)'
    ]]
];

$chart_data_json = json_encode($chart_data);

//Calcular cantidad de Usuarios
$total_usuarios = 0;
try{
    $sql_total_usuarios = "SELECT COUNT(id_usuario) AS total FROM usuarios WHERE role = 'estudiante'";
    $stmt_usuarios = $pdo->query($sql_total_usuarios);
    $resultado_usuarios = $stmt_usuarios->fetch(PDO::FETCH_ASSOC);
    $total_usuarios = $resultado_usuarios['total'] ?? 0;
} catch(PDOException $e){
    error_log("Error al cargar el total de usuarios: " . $e->getMessage());
    $total_usuarios = 0;
}

//Calcular cantidad de evaluaciones realizadas
$evaluaciones_hoy = 0;
try {
    $sql_evaluaciones_hoy = "SELECT COUNT(id) AS total FROM autoevaluaciones WHERE DATE(fecha_evaluacion) = CURDATE()";
    $stmt_hoy = $pdo->query($sql_evaluaciones_hoy);
    $resultado_hoy = $stmt_hoy->fetch(PDO::FETCH_ASSOC);
    $evaluaciones_hoy = $resultado_hoy['total'] ?? 0;
} catch (PDOException $e) {
    error_log("Error al cargar evaluaciones de hoy: " . $e->getMessage());
    $evaluaciones_hoy = 0;
}

$scores_map = array_combine($categorias, $scores_actuales);

$min_score = PHP_INT_MAX;
$min_category = 'N/A';

foreach ($scores_map as $category => $score) {
    if ($score < $min_score) {
        $min_score = $score;
        $min_category = $category;
    }
}



include ('../../includes/header.php');



?>

<br/>
<style>
    .chart-container {
        max-width: 600px; 
        margin: auto;
        padding-top: 15px; 
    }
    .main-title {
        font-weight: 700; 
        letter-spacing: 1px;
    }
    .metric-card {
        transition: transform 0.2s, box-shadow 0.2s; 
        border-radius: 12px; 
        border-left: 5px solid; 
    }
    .metric-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,.15) !important;
    }
    h4{
        color: white;
    }
    .metric-card.border-success { border-color: #198754 !important; }
    .metric-card.border-warning { border-color: #ffc107 !important; }
    .metric-card.border-danger { border-color: #dc3545 !important; }
    .card-header-main {
        background: linear-gradient(90deg, #007bff, #0056b3); 
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>

<body>
    <div class="container mt-5">
        
        <h1 class="mb-2 text-center text-primary main-title">
            <i class="fas fa-chart-line me-2"></i> Dashboard de Bienestar Estudiantil
        </h1>
        <p class="lead text-center text-secondary mb-5">
            Análisis Poblacional del Estado Emocional de los Estudiantes
        </p>

        ---

        <div class="row text-center mb-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 metric-card border-success shadow">
                    <div class="card-body">
                        <h5 class="card-title text-success mb-3"><i class="fas fa-users me-2"></i> Total Estudiantes</h5>
                        <p class="display-3 fw-bolder"><?php echo $total_usuarios; ?></p>
                        <small class="text-muted">Usuarios registrados en el sistema</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 metric-card border-warning shadow">
                    <div class="card-body">
                        <h5 class="card-title text-warning mb-3"><i class="fas fa-check-circle me-2"></i> Evaluaciones Hoy</h5>
                        <p class="display-3 fw-bolder"><?php echo $evaluaciones_hoy; ?></p>
                        <small class="text-muted">Evaluaciones completadas el día de hoy</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 metric-card border-danger shadow">
                    <div class="card-body">
                        <h5 class="card-title text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i> Riesgo Promedio</h5>
                        <p class="display-3 fw-bolder"><?php echo $min_score; ?></p>
                        <span class="badge bg-danger rounded-pill">Categoría: <?php echo $min_category; ?></span>
                    </div>
                </div>
            </div>
        </div>

        ---

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg border-0" style="border-radius: 12px;">
                    <div class="card-header card-header-main">
                        <h4 class="mb-0">
                            <i class="fas fa-bullseye me-2"></i> Foco de Bienestar (Promedio General)
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small text-center mb-4">
                            Puntuación promedio de bienestar en las categorías (0 = Peor, 5 = Mejor)
                        </p>
                        <div class="chart-container">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 mb-5">
            <a href="/modules/dashboard_admin/admin_export_data.php" class="btn btn-primary btn-lg shadow">
                <i class="fas fa-file-export me-2"></i> Exportar Datos Completos (CSV)
            </a> 
        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const chartData = <?php echo $chart_data_json; ?>;

            if (chartData.datasets && chartData.datasets.length > 0 && chartData.datasets[0].data.some(d => d > 0)) {
                const ctx = document.getElementById('radarChart').getContext('2d');
                
                const radarChart = new Chart(ctx, {
                    type: 'radar',
                    data: chartData,
                    options: {
                        responsive: true,
                        aspectRatio: 1, 
                        plugins: {
                            title: {
                                display: false,
                            },
                            legend: {
                                display: false 
                            }
                        },
                        scales: {
                            r: {
                                angleLines: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                suggestedMin: 0,
                                suggestedMax: 5, 
                                ticks: {
                                    stepSize: 1,
                                    color: 'rgba(0, 0, 0, 0.6)'
                                },
                                pointLabels: {
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                 const container = document.getElementById('radarChart').parentElement.parentElement;
                 container.innerHTML = '<div class="alert alert-info text-center mt-3" role="alert"> <i class="fas fa-info-circle me-2"></i> Aún no hay suficientes evaluaciones para mostrar el gráfico poblacional.</div>';
            }
        });
    </script>
</body>

<?php
    include ('../../includes/footer.php');
?>