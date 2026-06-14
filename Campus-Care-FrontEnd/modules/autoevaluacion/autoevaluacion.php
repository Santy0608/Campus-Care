<?php

require '../../includes/db.php';
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

$errores = [];
$mensajeExito = '';
$evaluacion_completada = false;
$datos_evaluacion_hoy = null;

if (!isset($pdo)){
    die('Error: No se pudo conectar a la base de datos');
}

// Verificar si ya existe una evaluación para hoy
$fecha_hoy = date('Y-m-d');
try {
    $sql_check_today = 'SELECT * FROM autoevaluaciones WHERE id_usuario = :id_usuario AND fecha_evaluacion = :fecha';
    $stmt_check_today = $pdo->prepare($sql_check_today);
    $stmt_check_today->execute(['id_usuario' => $id_usuario, 'fecha' => $fecha_hoy]);
    $datos_evaluacion_hoy = $stmt_check_today->fetch(PDO::FETCH_ASSOC);
    $evaluacion_completada = !empty($datos_evaluacion_hoy);
} catch (PDOException $e) {
    // Manejar error silenciosamente
}

// Manejar reinicio de evaluación
if (isset($_POST['reiniciar_evaluacion'])) {
    $evaluacion_completada = false;
    $datos_evaluacion_hoy = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reiniciar_evaluacion'])){
    $fecha_evaluacion = date('Y-m-d');

    $categorias = ['estres', 'ansiedad', 'sueno', 'estado_animo', 'relaciones', 'motivacion'];
    $data_to_insert = ['id_usuario' => $id_usuario, 'fecha_evaluacion' => $fecha_evaluacion];

    foreach($categorias as $cat){
        $score_field = $cat . '_score';
        $score = filter_input(INPUT_POST, $score_field, FILTER_VALIDATE_INT);

        if ($score === false || $score < 1 || $score > 5){
            $errores[$score_field] = "La puntuación de $cat es obligatoria y debe ser entre 1 y 5";
        } else {
            $data_to_insert[$score_field] = $score;
        }
    }

    if (empty($errores)){
        try{
            $sql_check = 'SELECT COUNT(*) FROM autoevaluaciones WHERE id_usuario = :id_usuario AND fecha_evaluacion = :fecha';
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute(['id_usuario' => $id_usuario, 'fecha' => $fecha_evaluacion]);
            // Almacena si la evaluación ya existe ANTES de hacer el INSERT/UPDATE
            $existe_evaluacion = $stmt_check->fetchColumn() > 0; 
            
            $es_nueva_evaluacion = !$existe_evaluacion; // <-- Nueva var

            if ($existe_evaluacion){
                $sql = "UPDATE autoevaluaciones SET 
                    estres_score = :estres_score, ansiedad_score = :ansiedad_score, sueno_score = :sueno_score, 
                    estado_animo_score = :estado_animo_score, relaciones_score = :relaciones_score, motivacion_score = :motivacion_score
                    WHERE id_usuario = :id_usuario AND fecha_evaluacion = :fecha_evaluacion";
                $mensaje_exito = '¡Tu autoevaluación de hoy ha sido actualizada!';
            } else {
                $sql = "INSERT INTO autoevaluaciones (id_usuario, fecha_evaluacion, estres_score, ansiedad_score, sueno_score, estado_animo_score, relaciones_score, motivacion_score)
                        VALUES (:id_usuario, :fecha_evaluacion, :estres_score, :ansiedad_score, :sueno_score, :estado_animo_score, :relaciones_score, :motivacion_score)";
                $mensaje_exito = '¡Autoevaluación registrada con éxito!';
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data_to_insert);


            //lógica de gamificación
            $puntos_ganados = 0;
            $sql_user = "SELECT racha_actual, puntos_totales, fecha_ultima_evaluacion FROM usuarios WHERE id_usuario = :id_usuario";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute(['id_usuario' => $id_usuario]);
            $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

            $racha_actual = $user_data['racha_actual'];
            $puntos_totales = $user_data['puntos_totales'];
            $ultima_fecha_db = $user_data['fecha_ultima_evaluacion'];

            $fecha_actual = new DateTime(date('Y-m-d'));
            $ultima_fecha = $ultima_fecha_db ? new DateTime($ultima_fecha_db) : null;

            //Hacemos el cálculo de puntos
            //Validación
            if ($es_nueva_evaluacion){
                $puntos_ganados += 50;

                $scores = array_filter($data_to_insert, fn($k) => str_ends_with($k, 'score'), ARRAY_FILTER_USE_KEY);
                $promedio = array_sum($scores) / count($scores);

                if ($promedio >= 4.0){
                    $puntos_ganados += 30; //Se hace un bonus por buen promedio
                }
            }

            //Hacemos el cálculo de racha
            $nueva_racha = $racha_actual;
            $dias_diferencia = 0;

            if($ultima_fecha){
                $intervalo = $ultima_fecha->diff($fecha_actual);
                $dias_diferencia = $intervalo->days;
            }

            if ($dias_diferencia === 1){
                $nueva_racha++;
            } elseif ($dias_diferencia > 1 || $ultima_fecha === null){
                $nueva_racha = 1;
            }

            $sql_update_user = "UPDATE usuarios SET 
            puntos_totales = puntos_totales + :puntos_ganados, 
            racha_actual = :nueva_racha, 
            fecha_ultima_evaluacion = :fecha_actual 
            WHERE id_usuario = :id_usuario";

            $stmt_update_user = $pdo->prepare($sql_update_user);
            $stmt_update_user->execute([
                'puntos_ganados' => $puntos_ganados,
                'nueva_racha' => $nueva_racha,
                'fecha_actual' => date('Y-m-d'),
                'id_usuario' => $id_usuario
            ]);
            
            // Agregar puntos ganados al mensaje de éxito si es una nueva entrada
            if ($puntos_ganados > 0) {
                $mensaje_exito .= " ¡Ganaste {$puntos_ganados} puntos!";
            }


            $sql_obtenidos = "SELECT id_logro FROM usuarios_logros WHERE id_usuario = :id_usuario";
            $stmt_obtenidos = $pdo->prepare($sql_obtenidos);
            $stmt_obtenidos->execute(['id_usuario' => $id_usuario]);
            $logros_obtenidos = $stmt_obtenidos->fetchAll(PDO::FETCH_COLUMN, 0); // Array de IDs ya obtenidos

            $sql_user_actualizado = "SELECT racha_actual, puntos_totales FROM usuarios WHERE id_usuario = :id_usuario";
            $stmt_user_actualizado = $pdo->prepare($sql_user_actualizado);
            $stmt_user_actualizado->execute(['id_usuario' => $id_usuario]);
            $user_data_actualizada = $stmt_user_actualizado->fetch(PDO::FETCH_ASSOC);

            $racha_actualizada = $user_data_actualizada['racha_actual'];
            $puntos_totales_actualizados = $user_data_actualizada['puntos_totales'];

            $sql_logros_disponibles = "SELECT id_logro, nombre, descripcion, criterio_puntos, criterio_racha FROM logros";
            $stmt_logros_disponibles = $pdo->prepare($sql_logros_disponibles);
            $stmt_logros_disponibles->execute();
            $logros_a_evaluar = $stmt_logros_disponibles->fetchAll(PDO::FETCH_ASSOC);

            $logros_ganados_ahora = [];
            foreach ($logros_a_evaluar as $logro) {
                if (in_array($logro['id_logro'], $logros_obtenidos)) {
                    continue;
                }

                $condicion_cumplida = false;
                

                if ($logro['criterio_puntos'] > 0 && $puntos_totales_actualizados >= $logro['criterio_puntos']) {
                    $condicion_cumplida = true;
                }
                
                if ($logro['criterio_racha'] > 0 && $racha_actualizada >= $logro['criterio_racha']) {
                    $condicion_cumplida = true;
                }

                
                if ($condicion_cumplida) {
                    $logros_ganados_ahora[] = $logro['id_logro']; // Añade el ID del logro ganado
                    $mensaje_exito .= " ¡Desbloqueaste el logro '{$logro['nombre']}'!";
                }
            }

            if (!empty($logros_ganados_ahora)) {
                $fecha_actual_db = date('Y-m-d H:i:s'); 
                
                $sql_insert_logro = "INSERT INTO usuarios_logros (id_usuario, id_logro, fecha_obtencion) VALUES ";
                $values = [];
                $params = ['id_usuario' => $id_usuario];
                $i = 0;
                
                foreach ($logros_ganados_ahora as $logro_id) { // Itera sobre los IDs ganados
                    $placeholder_logro = "id_logro$i";
                    $placeholder_fecha = "fecha_obtencion$i"; // Placeholder para la fecha
                    
                    $values[] = "(:id_usuario, :$placeholder_logro, :$placeholder_fecha)";
                    
                    $params[$placeholder_logro] = $logro_id;
                    $params[$placeholder_fecha] = $fecha_actual_db; 
                    $i++;
                }
                
                $sql_insert_logro .= implode(", ", $values);
                $stmt_insert = $pdo->prepare($sql_insert_logro);
                if (!$stmt_insert->execute($params)) {
                    $errorInfo = $stmt_insert->errorInfo();
                    error_log("Error al insertar logros: " . print_r($errorInfo, true));
                }
            }


            header('Location: /modules/dashboard/dashboard_progreso.php?msg=' . urlencode($mensaje_exito));
            exit;
        } catch(PDOException $e){
            $errores['db'] = "Error al guardar la autoevaluación " . $e->getMessage();
        }
    }

}

    $default_scores = [
        'estres_score' => 3, 'ansiedad_score' => 3, 'sueno_score' => 3, 
        'estado_animo_score' => 3, 'relaciones_score' => 3, 'motivacion_score' => 3
    ];

// Incluir el header después de toda la lógica de procesamiento y redirecciones
include ('../../includes/header.php');

?>

<style>
    /* Variables CSS fallback para navegadores que no soporten CSS custom properties */
    :root {
        --wellness-primary: #6B9BD1;
        --wellness-secondary: #8FBC8F;
        --wellness-accent: #DDA0DD;
        --wellness-light: #F8FBFF;
        --wellness-cream: #FAF9F6;
        --wellness-text: #4A5568;
        --wellness-text-light: #718096;
        --font-primary: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        --font-secondary: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        --transition-gentle: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Estilos específicos para autoevaluación */
    .evaluation-container {
        background: linear-gradient(135deg, #F8FBFF 0%, #FAF9F6 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .evaluation-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(107, 155, 209, 0.1);
        border: none;
        overflow: hidden;
        position: relative;
    }
    
    .evaluation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #6B9BD1, #8FBC8F, #DDA0DD);
    }
    
    /* Estilos para el resumen de evaluación */
    .evaluation-summary-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        transition: transform 0.2s ease;
        border: 2px solid transparent;
    }
    
    .evaluation-summary-item:hover {
        transform: translateY(-2px);
        border-color: var(--wellness-primary);
    }
    
    .evaluation-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .evaluation-score {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--wellness-primary);
    }
    
    .evaluation-title {
        color: #6B9BD1;
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        font-weight: 700;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .evaluation-subtitle {
        color: #718096;
        font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    
    .category-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,251,255,0.8) 100%);
        border: 1px solid rgba(107, 155, 209, 0.1);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
    }
    
    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #6B9BD1, #8FBC8F);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        transform-origin: left;
    }
    
    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(107, 155, 209, 0.15);
    }
    
    .category-card:hover::before {
        transform: scaleX(1);
    }
    
    .category-label {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        font-weight: 600;
        font-size: 1.2rem;
        color: #4A5568;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .range-container {
        position: relative;
        padding: 1rem 0;
    }
    
    .range-labels {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .range-label {
        font-size: 0.9rem;
        color: #718096;
        font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        font-weight: 500;
    }
    
    .form-range {
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, #ff6b6b 0%, #feca57 25%, #48dbfb 50%, #0abde3 75%, #10ac84 100%);
        border-radius: 50px;
        outline: none;
        -webkit-appearance: none;
        appearance: none;
    }
    
    .form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #6B9BD1;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(107, 155, 209, 0.3);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    .form-range::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 6px 20px rgba(107, 155, 209, 0.4);
    }
    
    .form-range::-moz-range-thumb {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #6B9BD1;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(107, 155, 209, 0.3);
        -moz-appearance: none;
    }
    
    .form-range::-moz-range-track {
        background: linear-gradient(90deg, #ff6b6b 0%, #feca57 25%, #48dbfb 50%, #0abde3 75%, #10ac84 100%);
        height: 8px;
        border-radius: 50px;
        border: none;
    }
    
    .range-value {
        text-align: center;
        margin-top: 1rem;
    }
    
    .range-value-number {
        font-size: 2rem;
        font-weight: 700;
        color: #6B9BD1;
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        text-shadow: 0 2px 4px rgba(107, 155, 209, 0.2);
        display: inline-block;
        min-width: 2rem;
    }
    
    .evaluation-buttons {
        margin-top: 2rem;
        text-align: center;
    }
    
    .btn-evaluation {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        font-weight: 600;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        border: none;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        margin: 0.5rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-evaluation::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s;
    }
    
    .btn-evaluation:hover::before {
        left: 100%;
    }
    
    .btn-evaluation:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        text-decoration: none;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #6B9BD1 0%, #5A8BC2 100%);
        color: white;
    }
    
    .btn-save:hover {
        background: linear-gradient(135deg, #5A8BC2 0%, #4A7BB2 100%);
        color: white;
    }
    
    .btn-progress {
        background: transparent;
        border: 2px solid #6B9BD1;
        color: #6B9BD1;
    }
    
    .btn-progress:hover {
        background: #6B9BD1;
        color: white;
    }
    
    .alert-custom {
        border-radius: 1rem;
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-danger-custom {
        background: linear-gradient(135deg, #fee, #fdd);
        color: #c53030;
        border-left: 4px solid #f56565;
    }
    
    /* Mejoras de accesibilidad */
    .form-range:focus {
        outline: 2px solid #6B9BD1;
        outline-offset: 2px;
    }
    
    /* Responsive mejorado */
    @media (max-width: 768px) {
        .evaluation-title {
            font-size: 2rem;
        }
        
        .btn-evaluation {
            width: 100%;
            margin: 0.5rem 0;
        }
        
        .category-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .evaluation-container {
            padding: 1rem 0;
        }
        
        .range-value-number {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .evaluation-title {
            font-size: 1.75rem;
        }
        
        .category-label {
            font-size: 1rem;
        }
        
        .evaluation-subtitle {
            font-size: 1rem;
        }
    }
</style>

<div class="evaluation-container">
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="evaluation-title">💙 Autoevaluación</h1>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card evaluation-card">
                    <div class="card-body p-5">
                        <h2 class="text-center evaluation-title">¿Cómo te sientes hoy?</h2>
                        <p class="text-center evaluation-subtitle">Evalúa tu estado en cada categoría (1 = Muy Malo, 5 = Excelente).</p>

                        <?php if ($evaluacion_completada): ?>
                            <div class="alert alert-success" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="alert-heading mb-2">¡Ya completaste tu autoevaluación de hoy!</h5>
                                        <p class="mb-2">Evaluación realizada: <strong><?= date('d/m/Y') ?></strong></p>
                                        
                                        <div class="row text-center mt-3">
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">😣</div>
                                                    <small class="text-muted d-block mb-1">Estrés</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['estres_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">😟</div>
                                                    <small class="text-muted d-block mb-1">Ansiedad</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['ansiedad_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">😴</div>
                                                    <small class="text-muted d-block mb-1">Sueño</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['sueno_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">😊</div>
                                                    <small class="text-muted d-block mb-1">Ánimo</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['estado_animo_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">💕</div>
                                                    <small class="text-muted d-block mb-1">Relaciones</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['relaciones_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="evaluation-summary-item">
                                                    <div class="evaluation-icon">💪</div>
                                                    <small class="text-muted d-block mb-1">Motivación</small>
                                                    <div class="evaluation-score"><?= $datos_evaluacion_hoy['motivacion_score'] ?><span class="text-muted">/5</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="my-3">
                                        
                                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="reiniciar_evaluacion" value="1">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-redo me-2"></i>Corregir evaluación
                                                </button>
                                            </form>
                                            <a href="../../modules/dashboard/dashboard_progreso.php" class="btn btn-primary">
                                                <i class="fas fa-chart-line me-2"></i>Ver mi progreso
                                            </a>
                                            <a href="../../index.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-home me-2"></i>Volver al inicio
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errores['db'])): ?>
                            <div class="alert alert-custom alert-danger-custom" role="alert">
                                <strong>Error:</strong> <?php echo htmlspecialchars($errores['db']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$evaluacion_completada): ?>
                        <form method="POST" action="autoevaluacion.php" onsubmit="return validateForm()">
                            <?php 
                            $labels = [
                                'estres' => '😣 Nivel de Estrés',
                                'ansiedad' => '😟 Nivel de Ansiedad',
                                'sueno' => '😴 Calidad del Sueño',
                                'estado_animo' => '😊 Estado de Ánimo General',
                                'relaciones' => '💕 Calidad de Relaciones',
                                'motivacion' => '💪 Nivel de Motivación',
                            ];
                            
                            foreach ($labels as $cat => $label):
                                $field_name = $cat . '_score';
                            ?>
                                <div class="category-card">
                                    <div class="category-label">
                                        <?php echo $label; ?>
                                    </div>
                                    <div class="range-container">
                                        <div class="range-labels">
                                            <span class="range-label">1 (Malo)</span>
                                            <span class="range-label">5 (Excelente)</span>
                                        </div>
                                        <input type="range" 
                                               class="form-range" 
                                               min="1" max="5" 
                                               name="<?php echo $field_name; ?>" 
                                               id="<?php echo $field_name; ?>" 
                                               value="<?php echo htmlspecialchars($_POST[$field_name] ?? $default_scores[$field_name]); ?>" 
                                               oninput="document.getElementById('value_<?php echo $field_name; ?>').textContent = this.value">
                                        <div class="range-value">
                                            <span class="range-value-number" id="value_<?php echo $field_name; ?>">
                                                <?php echo htmlspecialchars($_POST[$field_name] ?? $default_scores[$field_name]); ?>
                                            </span>
                                        </div>
                                        <?php if (isset($errores[$field_name])): ?>
                                            <div class="text-danger text-center mt-2"><?php echo $errores[$field_name]; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="evaluation-buttons">
                                <button type="submit" class="btn btn-evaluation btn-save">
                                    <i class="fas fa-heart me-2"></i>
                                    Guardar Evaluación Diaria
                                </button>
                                <a href="../dashboard/dashboard_progreso.php" class="btn btn-evaluation btn-progress">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Ver mi Progreso
                                </a>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para mejorar la interactividad -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mejorar la funcionalidad de los range sliders
    const ranges = document.querySelectorAll('.form-range');
    
    ranges.forEach(range => {
        // Función para actualizar el valor mostrado
        function updateValue() {
            const valueId = 'value_' + range.id;
            const valueElement = document.getElementById(valueId);
            if (valueElement) {
                valueElement.textContent = range.value;
                
                // Cambiar color del thumb según el valor
                const percentage = ((range.value - range.min) / (range.max - range.min)) * 100;
                range.style.setProperty('--thumb-position', percentage + '%');
            }
        }
        
        // Actualizar en input y change
        range.addEventListener('input', updateValue);
        range.addEventListener('change', updateValue);
        
        // Inicializar valor
        updateValue();
    });
    
    // Animación suave para las tarjetas
    const cards = document.querySelectorAll('.category-card');
    
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('fade-in');
    });
});

// Validación del formulario mejorada
function validateForm() {
    const ranges = document.querySelectorAll('.form-range');
    let isValid = true;
    
    ranges.forEach(range => {
        if (!range.value || range.value < 1 || range.value > 5) {
            isValid = false;
            range.style.borderColor = '#ff6b6b';
        } else {
            range.style.borderColor = '';
        }
    });
    
    if (!isValid) {
        alert('Por favor, asegúrate de que todas las categorías tengan una puntuación entre 1 y 5.');
        return false;
    }
    
    return true;
}
</script>

<style>
/* Animaciones adicionales */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fade-in 0.6s ease-out forwards;
}
</style>

<?php include('../../includes/footer.php'); ?>


